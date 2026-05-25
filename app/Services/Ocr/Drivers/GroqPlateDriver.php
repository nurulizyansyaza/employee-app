<?php

namespace App\Services\Ocr\Drivers;

use App\Services\Ocr\PlateOcrDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Groq (OpenAI-compatible) vision OCR driver.
 *
 * Configure:
 *   OCR_PROVIDER=groq
 *   GROQ_API_KEY=gsk_...
 *   GROQ_OCR_MODEL=meta-llama/llama-4-scout-17b-16e-instruct   (optional)
 */
class GroqPlateDriver implements PlateOcrDriver
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'meta-llama/llama-4-scout-17b-16e-instruct',
    ) {}

    public function read(string $absolutePath): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('GROQ_API_KEY is not configured.');
        }

        $mime    = mime_content_type($absolutePath) ?: 'image/jpeg';
        $base64  = base64_encode((string) file_get_contents($absolutePath));
        $dataUri = "data:{$mime};base64,{$base64}";

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->model,
                'temperature' => 0,
                'max_tokens'  => 32,
                'messages'    => [[
                    'role'    => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Read the car license plate in this image (any country). Reply with ONLY the plate text in uppercase, using single spaces between letter/digit groups (e.g. "B 1234 CD", "ABC 1234", "AB12 CDE"). If unreadable, reply UNKNOWN.',
                        ],
                        [
                            'type'      => 'image_url',
                            'image_url' => ['url' => $dataUri],
                        ],
                    ],
                ]],
            ]);

        if (! $response->ok()) {
            $body = (string) $response->body();
            if (strlen($body) > 500) {
                $body = substr($body, 0, 500) . '...';
            }
            throw new RuntimeException(
                'Groq request failed: HTTP ' . $response->status() . ' — ' . $body
            );
        }

        $text = (string) ($response->json('choices.0.message.content') ?? '');

        return [
            'raw_text'   => trim($text),
            'confidence' => null,
        ];
    }

    public function providerName(): string
    {
        return 'groq:' . $this->model;
    }
}
