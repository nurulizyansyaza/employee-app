<?php

namespace App\Services\Ocr\Drivers;

use App\Services\Ocr\PlateOcrDriver;

class FakePlateDriver implements PlateOcrDriver
{
    public function read(string $absolutePath): array
    {
        $samples = ['B 1234 CD', '7XYZ 123', 'AB12 CDE', 'S AB 1234', 'ABC 1234', 'XYZ 999'];
        $idx     = abs(crc32(basename($absolutePath))) % count($samples);

        return [
            'raw_text'   => $samples[$idx],
            'confidence' => 0.99,
        ];
    }

    public function providerName(): string
    {
        return 'fake';
    }
}
