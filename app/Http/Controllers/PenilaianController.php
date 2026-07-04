<?php

namespace App\Http\Controllers;

use App\Services\AnswerBuilder;
use App\Services\FammiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Debugbar;

class PenilaianController extends Controller
{
    public function __construct(
        protected FammiApiService $api,
        protected AnswerBuilder $builder,
    ) {}

    /**
     * Render the main assessment wizard page.
     */
    public function index()
    {
        return view('penilaian', [
            'debugMode' => config('fammi.debug_mode', true),
        ]);
    }
    public function bulanan()
    {
        return view('bulanan', [
            'debugMode' => config('fammi.debug_mode', true),
        ]);
    }
    /**
     * GET /api/classes
     * Return list of classes from config.
     */
    public function getClasses(): JsonResponse
    {
        $classes = collect(config('fammi.classes'))
            ->map(fn(string $name, string $code) => [
                'code' => $code,
                'name' => $name,
            ])
            ->values();

        return response()->json(['classes' => $classes]);
    }

    /**
     * GET /api/teachers
     * Fetch teacher list from Fammi data source.
     */
    public function getTeachers(): JsonResponse
    {

        $teachers = $this->api->getDataSource(config('fammi.guru_data_source'));
        return response()->json(['options' => $teachers]);
    }

    /**
     * GET /api/students?kode_kelas=...
     * Fetch student list by extracting the data source slug from quiz questions.
     */
    public function getStudents(Request $request): JsonResponse
    {
        $kodeKelas = $request->query('kode_kelas');

        if (!$kodeKelas) {
            return response()->json(['error' => 'Parameter kode_kelas wajib diisi'], 400);
        }

        // 1. Get additional content ID
        $additionalContentId = $this->api->getAdditionalContent($kodeKelas);
        if (!$additionalContentId) {
            return response()->json(['error' => 'Gagal mengambil data kelas'], 500);
        }

        // 2. Get quiz programs
        $programs = $this->api->getQuizPrograms($additionalContentId);
        if (empty($programs)) {
            return response()->json(['error' => 'Tidak ada program quiz ditemukan'], 500);
        }

        // 3. Get questions from the first quiz to find student data source slug
        $questions = $this->api->getQuizQuestions($programs[0]['quizId']);

        $studentSlug = null;
        foreach ($questions as $q) {
            if (($q['quizType'] ?? '') === 'data_source_multiple_no_option') {
                $detail = json_decode($q['detail'] ?? '{}', true);
                if (isset($detail['options']) && is_string($detail['options'])) {
                    $studentSlug = str_replace('.json', '', $detail['options']);
                    break;
                }
            }
        }

        if (!$studentSlug) {
            return response()->json(['error' => 'Data source siswa tidak ditemukan'], 500);
        }

        // 4. Fetch student list
        $students = $this->api->getDataSource($studentSlug);

        return response()->json(['options' => $students]);
    }

    /**
     * POST /api/submit
     * Process and submit all character assessments.
     *
     * Flow:
     *   1. Fetch additional content → quiz programs
     *   2. Filter quizzes (EMPATI, RESILIENCE, INISIATIF, 7 KEBIASAAN — exclude Catatan Bulanan)
     *   3. For each quiz → fetch questions → extract indicators
     *   4. For each indicator → build answer payload → POST to API
     */
    public function submit(Request $request): JsonResponse
    {
        set_time_limit(300); // Allow up to 5 minutes for all submissions

        $request->validate([
            'guru'             => 'required|string',
            'kode_kelas'       => 'required|string',
            'bulan'            => 'required|string',
            'siswa'            => 'required|array',
            'siswa.konsisten'  => 'present|array',
            'siswa.sering'     => 'present|array',
            'siswa.kadang'     => 'present|array',
            'siswa.belum'      => 'present|array',
        ]);

        $guru       = $request->input('guru');
        $kodeKelas  = $request->input('kode_kelas');
        $bulan      = $request->input('bulan');
        $siswa      = $request->input('siswa');

        $siswaKonsisten = $siswa['konsisten'] ?? [];
        $siswaSering    = $siswa['sering'] ?? [];
        $siswaKadang    = $siswa['kadang'] ?? [];
        $siswaBelum     = $siswa['belum'] ?? [];

        // Debug mode: build payloads but don't submit
        $debugMode = $request->boolean('debug', config('fammi.debug_mode', false));

        // ── 1. Get additional content ───────────────────────────
        $additionalContentId = $this->api->getAdditionalContent($kodeKelas);
        if (!$additionalContentId) {
            return response()->json([
                'success' => false,
                'error'   => 'Gagal mengambil data kelas. Pastikan kode kelas valid.',
            ], 500);
        }

        // ── 2. Get & filter quiz programs ───────────────────────
        $allPrograms = $this->api->getQuizPrograms($additionalContentId);

        $filters  = config('fammi.quiz_filters');
        $excludes = config('fammi.quiz_exclude_filters');

        $programs = collect($allPrograms)->filter(function (array $p) use ($filters, $excludes) {
            $title = $p['title'] ?? '';
            $titleUpper = mb_strtoupper($title);

            // Exclude blacklisted keywords
            foreach ($excludes as $exclude) {
                if (str_contains($titleUpper, mb_strtoupper($exclude))) {
                    return false;
                }
            }

            // Include only whitelisted keywords
            foreach ($filters as $filter) {
                if (str_contains($titleUpper, mb_strtoupper($filter))) {
                    return true;
                }
            }

            return false;
        })->values();

        if ($programs->isEmpty()) {
            return response()->json([
                'success' => false,
                'error'   => 'Tidak ada quiz penilaian karakter ditemukan untuk kelas ini.',
            ], 500);
        }

        // ── 3 & 4. Process each quiz × each indicator ───────────
        $results   = [];
        $total     = 0;
        $completed = 0;
        $failed    = 0;

        foreach ($programs as $program) {
            $quizId    = $program['quizId'];
            $quizTitle = $program['title'];

            // Fetch quiz questions
            $questions = $this->api->getQuizQuestions($quizId);
            if (empty($questions)) {
                $results[] = [
                    'quiz'      => $quizTitle,
                    'indicator' => '-',
                    'status'    => 'failed',
                    'message'   => 'Gagal mengambil soal quiz',
                ];
                $total++;
                $failed++;
                continue;
            }

            // Extract indicator data source
            $indicators = [];
            foreach ($questions as $q) {
                if (($q['quizType'] ?? '') === 'data_source'
                    && str_contains(mb_strtolower($q['title'] ?? ''), 'indikator')
                ) {
                    $detail = json_decode($q['detail'] ?? '{}', true);
                    if (isset($detail['options']) && is_string($detail['options'])) {
                        $slug       = str_replace('.json', '', $detail['options']);
                        $indicators = $this->api->getDataSource($slug);
                    }
                    break;
                }
            }

            if (empty($indicators)) {
                $results[] = [
                    'quiz'      => $quizTitle,
                    'indicator' => '-',
                    'status'    => 'failed',
                    'message'   => 'Tidak ada indikator ditemukan',
                ];
                $total++;
                $failed++;
                continue;
            }

            // Loop each indicator → build payload → submit (or collect in debug)
            foreach ($indicators as $indicator) {
                $total++;

                $answers = $this->builder->buildAnswers(
                    $questions,
                    $bulan,
                    $indicator,
                    $siswaKonsisten,
                    $siswaSering,
                    $siswaKadang,
                    $siswaBelum,
                    $guru,
                );

                if ($debugMode) {
                    // Debug: collect payload without submitting
                    $completed++;
                    $results[] = [
                        'quiz'      => $quizTitle,
                        'quizId'    => $quizId,
                        'indicator' => $indicator,
                        'status'    => 'debug',
                        'message'   => 'Debug — tidak dikirim',
                        'payload'   => $answers,
                    ];
                } else {
                    // Production: actually submit to API
                    $result = $this->api->submitAnswer($quizId, $answers);

                    if ($result['success']) {
                        $completed++;
                        $results[] = [
                            'quiz'      => $quizTitle,
                            'indicator' => $indicator,
                            'status'    => 'success',
                            'message'   => 'Berhasil dikirim',
                            'url'      => $result['url'] ?? null,
                        ];
                    } else {
                        $failed++;
                        $results[] = [
                            'quiz'      => $quizTitle,
                            'indicator' => $indicator,
                            'status'    => 'failed',
                            'message'   => 'Gagal: HTTP ' . ($result['status'] ?? 'unknown'),
                            'url'      => $result['url'] ?? null,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success'   => $failed === 0,
            'debug'     => $debugMode,
            'total'     => $total,
            'completed' => $completed,
            'failed'    => $failed,
            'results'   => $results,
        ]);
    }

    /**
     * POST /api/submit-bulanan
     * Process and submit all character assessments.
     *
     * Flow:
     *   1. Fetch additional content → quiz programs
     *   2. Filter quizzes (Exclude -> EMPATI, RESILIENCE, INISIATIF, 7 KEBIASAAN — include Catatan Bulanan)
     *   3. For each quiz → fetch questions → extract indicators
     *   4. For each indicator → build answer payload → POST to API
     */
    public function submitBulanan(Request $request): JsonResponse
    {
        set_time_limit(300); // Allow up to 5 minutes for all submissions

        $request->validate([
            'guru'             => 'required|string',
            'kode_kelas'       => 'required|string',
            'bulan'            => 'required|string',
            'siswa'            => 'required|array',
            'siswa.konsisten'  => 'present|array',
            'komentar'          => 'required',
            'jenis_catatan'       => 'required',
        ]);

        $guru       = $request->input('guru');
        $kodeKelas  = $request->input('kode_kelas');
        $bulan      = $request->input('bulan');
        $siswa      = $request->input('siswa');
        $komentar   = $request->input('komentar');
        $jenisCatatan = $request->input('jenis_catatan');

        $kumpulan_siswa = $siswa['konsisten'] ?? [];

        // Debug mode: build payloads but don't submit
        $debugMode = $request->boolean('debug', config('fammi.debug_mode', false));

        // ── 1. Get additional content ───────────────────────────
        $additionalContentId = $this->api->getAdditionalContent($kodeKelas);
        if (!$additionalContentId) {
            return response()->json([
                'success' => false,
                'error'   => 'Gagal mengambil data kelas. Pastikan kode kelas valid.',
            ], 500);
        }

        // ── 2. Get & filter quiz programs ───────────────────────
        $allPrograms = $this->api->getQuizPrograms($additionalContentId);

        $filters  = config('fammi.quiz_filters');
        $excludes = config('fammi.quiz_exclude_filters');

        $programs = collect($allPrograms)->filter(function (array $p) use ($filters, $excludes) {
            $title = $p['title'] ?? '';
            $titleUpper = mb_strtoupper($title);

            // Includes blacklisted keywords
            foreach ($excludes as $exclude) {
                if (str_contains($titleUpper, mb_strtoupper($exclude))) {
                    return true;
                }
            }

            // Exclude whitelisted keywords
            foreach ($filters as $filter) {
                if (str_contains($titleUpper, mb_strtoupper($filter))) {
                    return false;
                }
            }

            return false;
        })->values();

        if ($programs->isEmpty()) {
            return response()->json([
                'success' => false,
                'error'   => 'Tidak ada quiz Catatan bulanan ditemukan untuk kelas ini.',
            ], 500);
        }

        // ── 3 & 4. Process each quiz × each indicator ───────────
        $results   = [];
        $total     = 0;
        $completed = 0;
        $failed    = 0;

        foreach ($programs as $program) {
            $quizId    = $program['quizId'];
            $quizTitle = $program['title'];

            // Fetch quiz questions
            $questions = $this->api->getQuizQuestions($quizId);
            if (empty($questions)) {
                $results[] = [
                    'quiz'      => $quizTitle,
                    'indicator' => '-',
                    'status'    => 'failed',
                    'message'   => 'Gagal mengambil soal quiz',
                ];
                $total++;
                $failed++;
                continue;
            }

            // Extract indicator data source
            $indicators = [];
            foreach ($questions as $q) {
                if (($q['quizType'] ?? '') === 'data_source'
                    && str_contains(mb_strtolower($q['title'] ?? ''), 'indikator')
                ) {
                    $detail = json_decode($q['detail'] ?? '{}', true);
                    if (isset($detail['options']) && is_string($detail['options'])) {
                        $slug       = str_replace('.json', '', $detail['options']);
                        $indicators = $this->api->getDataSource($slug);
                    }
                    break;
                }
            }

            if (empty($indicators)) {
                $results[] = [
                    'quiz'      => $quizTitle,
                    'indicator' => '-',
                    'status'    => 'failed',
                    'message'   => 'Tidak ada indikator ditemukan',
                ];
                $total++;
                $failed++;
                continue;
            }

            // Loop each siswa → build payload → submit (or collect in debug)
            foreach ($kumpulan_siswa as $siswa) {
                $total++;

                $answers = $this->builder->buildAnswersBulanan(
                    $questions,
                    $bulan,
                    $komentar,
                    $jenisCatatan,
                    $siswa,
                    $guru,
                );

                if ($debugMode) {
                    // Debug: collect payload without submitting
                    $completed++;
                    $results[] = [
                        'quiz'      => $quizTitle,
                        'quizId'    => $quizId,
                        'siswa' => $siswa,
                        'status'    => 'debug',
                        'message'   => 'Debug — tidak dikirim',
                        'payload'   => $answers,
                    ];
                } else {
                    // Production: actually submit to API
                    $result = $this->api->submitAnswer($quizId, $answers);

                    if ($result['success']) {
                        $completed++;
                        $results[] = [
                            'quiz'      => $quizTitle,
                            'siswa' => $siswa,
                            'payload'  => $answers,
                            'status'    => 'success',
                            'message'   => 'Berhasil dikirim',
                            'url'      => $result['url'] ?? null,
                        ];
                    } else {
                        $failed++;
                        $results[] = [
                            'quiz'      => $quizTitle,
                            'siswa' => $siswa,
                            'payload'  => $answers,
                            'status'    => 'failed',
                            'message'   => 'Gagal: HTTP ' . ($result['status'] ?? 'unknown'),
                            'url'      => $result['url'] ?? null,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success'   => $failed === 0,
            'debug'     => $debugMode,
            'total'     => $total,
            'completed' => $completed,
            'failed'    => $failed,
            'results'   => $results,
        ]);
    }
}
