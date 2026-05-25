<?php

namespace App\Providers;

use App\Services\Ocr\Drivers\FakePlateDriver;
use App\Services\Ocr\Drivers\GeminiPlateDriver;
use App\Services\Ocr\Drivers\GroqPlateDriver;
use App\Services\Ocr\Drivers\OpenAiPlateDriver;
use App\Services\Ocr\PlateNormalizer;
use App\Services\Ocr\PlateOcrDriver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlateNormalizer::class);

        $this->app->bind(PlateOcrDriver::class, function () {
            $provider = strtolower((string) env('OCR_PROVIDER', 'fake'));

            return match ($provider) {
                'openai' => new OpenAiPlateDriver(
                    (string) env('OPENAI_API_KEY', ''),
                    (string) env('OPENAI_OCR_MODEL', 'gpt-4o-mini'),
                ),
                'gemini' => new GeminiPlateDriver(
                    (string) env('GEMINI_API_KEY', ''),
                    (string) env('GEMINI_OCR_MODEL', 'gemini-2.0-flash'),
                ),
                'groq' => new GroqPlateDriver(
                    (string) env('GROQ_API_KEY', ''),
                    (string) env('GROQ_OCR_MODEL', 'meta-llama/llama-4-scout-17b-16e-instruct'),
                ),
                default => new FakePlateDriver(),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
