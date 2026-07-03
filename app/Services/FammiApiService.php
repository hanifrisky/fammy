<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FammiApiService
{
    protected string $baseUrl;
    protected string $dataSourceUrl;
    protected string $authToken1;
    protected string $authToken2;
    protected string $programToken;
    protected string $refId;

    public function __construct()
    {
        $this->baseUrl       = config('fammi.base_url');
        $this->dataSourceUrl = config('fammi.data_source_url');
        $this->authToken1    = config('fammi.auth_token_1');
        $this->authToken2    = config('fammi.auth_token_2');
        $this->programToken  = config('fammi.program_token');
        $this->refId         = (string) \Illuminate\Support\Str::uuid();
    }

    /**
     * Get additionalContentId from a class shortener code.
     *
     * GET {base_url}/s/{kodeKelas}
     * → extract additionalContentId from the returned URL query param
     */
    public function getAdditionalContent(string $kodeKelas): ?string
    {
        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/s/{$kodeKelas}");

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['url'])) {
                    parse_str(parse_url($data[0]['url'], PHP_URL_QUERY), $params);
                    return $params['additionalContentId'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error("FammiApi::getAdditionalContent failed for {$kodeKelas}", [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Get quiz programs for a given additionalContentId.
     *
     * GET {base_url}/quiz_program/{additionalContentId}/{programToken}/1?ref_id={refId}
     */
    public function getQuizPrograms(string $additionalContentId): array
    {
        try {
            $url = "{$this->baseUrl}/quiz_program/{$additionalContentId}/{$this->programToken}/1";
            $response = Http::timeout(15)->get($url, [
                'ref_id' => $this->refId,
            ]);

            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error("FammiApi::getQuizPrograms failed", [
                'additionalContentId' => $additionalContentId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get quiz questions for a given quizId.
     *
     * GET {base_url}/quiz/{quizId}
     */
    public function getQuizQuestions(string $quizId): array
    {
        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/quiz/{$quizId}");
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error("FammiApi::getQuizQuestions failed for {$quizId}", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get data source options (teachers, students, indicators, etc).
     *
     * GET {data_source_url}/{slug}
     * slug can include or exclude .json extension
     */
    public function getDataSource(string $slug): array
    {
        try {
            $slug = str_replace('.json', '', $slug);
            $response = Http::timeout(15)->get("{$this->dataSourceUrl}/{$slug}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['options'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("FammiApi::getDataSource failed for {$slug}", [
                'error' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Submit answers for a quiz.
     *
     * POST {base_url}/answer/{quizId}/{authToken1}/{authToken2}?ref_id={refId}
     */
    public function submitAnswer(string $quizId, array $answers): array
    {
        try {
            $url = "{$this->baseUrl}/answer/{$quizId}/{$this->authToken1}/{$this->authToken2}?ref_id={$this->refId}";

            $response = Http::timeout(30)->post($url, $answers);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error("FammiApi::submitAnswer failed for quiz {$quizId}", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 0,
                'body'    => ['error' => $e->getMessage()],
            ];
        }
    }
}
