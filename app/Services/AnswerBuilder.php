<?php

namespace App\Services;

class AnswerBuilder
{
    /**
     * Build a single answer payload for one quiz + one indicator.
     *
     * Skips questions with quizType "section" and "sub_question".
     * Maps each remaining question to the correct user-provided answer.
     *
     * @param  array  $questions       Quiz questions from the API
     * @param  string $bulan           Selected month (e.g. "Juni 2026")
     * @param  string $indicator       Current indicator being looped
     * @param  array  $siswaKonsisten  Students in KONSISTEN MUNCUL
     * @param  array  $siswaSering     Students in SERING MUNCUL
     * @param  array  $siswaKadang     Students in KADANG MUNCUL
     * @param  array  $siswaBelum      Students in BELUM MUNCUL
     * @param  string $guru            Selected teacher name
     * @return array                   Array of [{id, answered}, ...]
     */
    public function buildAnswers(
        array  $questions,
        string $bulan,
        string $indicator,
        array  $siswaKonsisten,
        array  $siswaSering,
        array  $siswaKadang,
        array  $siswaBelum,
        string $guru,
    ): array {
        $answers = [];

        foreach ($questions as $question) {
            $quizType = $question['quizType'] ?? '';
            $title    = $question['title'] ?? '';
            $rowId    = $question['rowId'] ?? '';

            // Skip sections and sub_questions
            if (in_array($quizType, ['section', 'sub_question'], true)) {
                continue;
            }

            $answered = $this->resolveAnswer(
                $quizType,
                $title,
                $bulan,
                $indicator,
                $siswaKonsisten,
                $siswaSering,
                $siswaKadang,
                $siswaBelum,
                $guru,
            );

            if ($answered !== null) {
                $answers[] = [
                    'id'       => $rowId,
                    'answered' => $answered,
                ];
            }
        }

        return $answers;
    }

    public function buildAnswersBulanan(
        array  $questions,
        string $bulan,
        string $komentar,
        string $jenisCatatan,
        string  $siswa,
        string $guru,
    ): array {
        $answers = [];

        foreach ($questions as $question) {
            $quizType = $question['quizType'] ?? '';
            $title    = $question['title'] ?? '';
            $rowId    = $question['rowId'] ?? '';

            // Skip sections and sub_questions
            if (in_array($quizType, ['section', 'sub_question'], true)) {
                continue;
            }

            $answered = $this->resolveAnswerBulanan(
                $quizType,
                $title,
                $bulan,
                $komentar,
                $jenisCatatan,
                $siswa,
                $guru,
            );

            if ($answered !== null) {
                $answers[] = [
                    'id'       => $rowId,
                    'answered' => $answered,
                ];
            }
        }

        return $answers;
    }

    /**
     * Determine the answer for a single question based on its type and title.
     */
    protected function resolveAnswer(
        string $quizType,
        string $title,
        string $bulan,
        string $indicator,
        array  $siswaKonsisten,
        array  $siswaSering,
        array  $siswaKadang,
        array  $siswaBelum,
        string $guru,
    ): ?string {
        $titleLower = mb_strtolower($title);

        // ─── Bulan penilaian ──────────────────────────────────────
        if ($quizType === 'single_choice' && str_contains($titleLower, 'bulan penilaian')) {
            return $bulan;
        }

        // ─── Pilih indikator ─────────────────────────────────────
        if ($quizType === 'data_source' && str_contains($titleLower, 'indikator')) {
            return $indicator;
        }

        // ─── Siswa KONSISTEN MUNCUL ──────────────────────────────
        if ($quizType === 'data_source_multiple_no_option' && str_contains($title, 'KONSISTEN MUNCUL')) {
            return $this->formatStudentAnswer($siswaKonsisten);
        }

        // ─── Siswa SERING MUNCUL ─────────────────────────────────
        if ($quizType === 'data_source_multiple_no_option' && str_contains($title, 'SERING MUNCUL')) {
            return $this->formatStudentAnswer($siswaSering);
        }

        // ─── Siswa KADANG MUNCUL ─────────────────────────────────
        if ($quizType === 'data_source_multiple_no_option' && str_contains($title, 'KADANG MUNCUL')) {
            return $this->formatStudentAnswer($siswaKadang);
        }

        // ─── Siswa BELUM MUNCUL ──────────────────────────────────
        if ($quizType === 'data_source_multiple_no_option' && str_contains($title, 'BELUM MUNCUL')) {
            return $this->formatStudentAnswer($siswaBelum);
        }

        // ─── Nama guru / observer ────────────────────────────────
        if ($quizType === 'data_source' && str_contains($titleLower, 'nama lengkap')) {
            return $guru;
        }

        // ─── Konfirmasi persetujuan ──────────────────────────────
        if ($quizType === 'single_choice' && str_contains($titleLower, 'dengan ini saya')) {
            return 'Ya, saya menyetujui pernyataan ini';
        }

        return null;
    }

    protected function resolveAnswerBulanan(
        string $quizType,
        string $title,
        string $bulan,
        string $komentar,
        string $jenisCatatan,
        string $siswa,
        string $guru,
    ): ?string {
        $titleLower = mb_strtolower($title);

        // ─── Siswa ──────────────────────────────
        if ($quizType === 'data_source' && str_contains($title, 'nama siswa')) {
            return $siswa;
        }

        // ─── Bulan penilaian ──────────────────────────────────────
        if ($quizType === 'single_choice' && str_contains($titleLower, 'bulan penilaian')) {
            return $bulan;
        }

        // ─── Masukan jenis catatan ─────────────────────────────────────
        if ($quizType === 'free_text' && str_contains($titleLower, 'Masukkan catatan')) {
            return $komentar;
        }

        // ─── Pilih jenis catatan ─────────────────────────────────────
        if ($quizType === 'multiple_choice' && str_contains($titleLower, 'Jenis catatan')) {
            return $jenisCatatan;
        }

        // ─── Nama guru / observer ────────────────────────────────
        if ($quizType === 'data_source' && str_contains($titleLower, 'nama lengkap')) {
            return $guru;
        }

        // ─── Konfirmasi persetujuan ──────────────────────────────
        if ($quizType === 'single_choice' && str_contains($titleLower, 'dengan ini saya')) {
            return 'Ya, saya menyetujui pernyataan ini';
        }

        return null;
    }

    /**
     * Format student list to comma-separated string, or fallback text.
     */
    protected function formatStudentAnswer(array $students): string
    {
        if (empty($students)) {
            return 'Tidak ada murid dalam kategori ini';
        }

        return implode(', ', $students);
    }
}
