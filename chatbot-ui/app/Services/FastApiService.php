<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FastApiService
{
    public function analyze(string $text, string $method = 'transformer', int $topN = 2): array
    {
        $baseUrl = rtrim(config('services.fastapi.url'), '/');

        $response = Http::timeout(30)
            ->acceptJson()
            ->post("{$baseUrl}/analyze", [
                'text' => $text,
                'method' => $method,
                'top_n' => $topN,
            ]);

        $response->throw();

        return $response->json();
    }
}