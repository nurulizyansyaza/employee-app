<?php

namespace App\Services\Ocr;

interface PlateOcrDriver
{
    /**
     * Read raw text from a car-plate image.
     *
     * @return array{raw_text: string, confidence: ?float}
     */
    public function read(string $absolutePath): array;

    public function providerName(): string;
}
