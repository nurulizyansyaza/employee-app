<?php

namespace App\Services\Ocr\Drivers;

use App\Services\Ocr\PlateOcrDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Google Gemini (vision) prompted OCR driver.
 *
 * Configure:
 *   OCR_PROVIDER=gemini
 *   GEMINI_API_KEY=...                       (from https://aistudio.google.com/apikey)
 *   GEMINI_OCR_MODEL=gemini-2.0-flash        (optional)
 *
 * Free tier (AI Studio): ~15 req/min, ~1500 req/day for gemini-2.0-flash.
 */
class GeminiPlateDriver implements PlateOcrDriver
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'gemini-2.0-flash',
    ) {}

    public function read(string $absolutePath): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $mime   = mime_content_type($absolutePath) ?: 'image/jpeg';
        $base64 = base64_encode((string) file_get_contents($absolutePath));

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = Http::withHeaders(['x-goog-api-key' => $this->apiKey])
            ->timeout(30)
            ->post($url, [
                'contents' => [[
                    'parts' => [
                        [
                            'text' => 'Read the car license plate in this image (any country). Reply with ONLY the plate text in uppercase, using single spaces between letter/digit groups (e.g. "B 1234 CD", "ABC 1234", "AB12 CDE"). If unreadable, reply UNKNOWN.',
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $mime,
                                'data'      => $base64,
                            ],
                        ],
                    ],
                ]],
                'generationConfig' => [
                    'temperature'     => 0,
                    'maxOutputTokens' => 32,
                ],
            ]);

        if (! $response->ok()) {
            $body = (string) $response->body();
            if (strlen($body) > 500) {
                $body = substr($body, 0, 500) . '...';
            }
            throw new RuntimeException(
                'Gemini request failed: HTTP ' . $response->status() . ' — ' . $body
            );
        }

        $text = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');

        return [
            'raw_text'   => trim($text),
            'confidence' => null,
        ];
    }

    public function providerName(): string
    {
        return 'gemini:' . $this->model;
    }
}
