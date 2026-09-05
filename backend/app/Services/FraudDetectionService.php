<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FraudDetectionService
{
    /**
     * Check phone + district against BDCouriers fraud API.
     * Returns risk score (0–100) and flags.
     */
    public function check(string $phone, string $district): array
    {
        $apiKey = config('services.bdcouriers.api_key');

        if (! $apiKey) {
            return ['score' => 0, 'flags' => [], 'source' => 'skipped'];
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                ->post('https://api.bdcouriers.com/v1/fraud-check', [
                    'phone'    => $phone,
                    'district' => $district,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'score'  => $data['risk_score'] ?? 0,
                    'flags'  => $data['flags'] ?? [],
                    'source' => 'bdcouriers',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('FraudDetection API failed', ['error' => $e->getMessage()]);
        }

        // Soft fail — don't block orders if API is down
        return ['score' => 0, 'flags' => [], 'source' => 'fallback'];
    }
}
