<?php

namespace Tests\Unit;

use App\Services\Ocr\PlateNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PlateNormalizerTest extends TestCase
{
    private PlateNormalizer $normalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->normalizer = new PlateNormalizer();
    }

    public function test_normalize_uppercases_and_strips_noise(): void
    {
        $this->assertSame('B 1234 CD', $this->normalizer->normalize('  b  1234  cd!! '));
        $this->assertSame('ABC 1234', $this->normalizer->normalize('abc.1234'));
    }

    /**
     * @dataProvider validPlateProvider
     */
    #[DataProvider('validPlateProvider')]
    public function test_matches_format_accepts_international_plates(string $plate): void
    {
        $this->assertTrue(
            $this->normalizer->matchesFormat($plate),
            "Expected '{$plate}' to be a valid plate format"
        );
    }

    public static function validPlateProvider(): array
    {
        return [
            'Indonesia'   => ['B 1234 CD'],
            'US-style'    => ['ABC 1234'],
            'UK'          => ['AB12 CDE'],
            'Germany'     => ['M-AB 1234'],
            'California'  => ['7XYZ123'],
            'Singapore'   => ['SGP 1234A'],
        ];
    }

    /**
     * @dataProvider invalidPlateProvider
     */
    #[DataProvider('invalidPlateProvider')]
    public function test_matches_format_rejects_invalid_strings(string $plate): void
    {
        $this->assertFalse(
            $this->normalizer->matchesFormat($plate),
            "Expected '{$plate}' to be rejected"
        );
    }

    public static function invalidPlateProvider(): array
    {
        return [
            'letters only' => ['ABCDEF'],
            'digits only'  => ['123456'],
            'too short'    => ['A'],
            'too long'     => ['ABCDEFGH123456789'],
            'lowercase'    => ['b 1234 cd'],
            'symbols'      => ['B@1234#CD'],
        ];
    }

    public function test_extract_plate_returns_full_string_when_it_matches(): void
    {
        $this->assertSame('B 1234 CD', $this->normalizer->extractPlate('B 1234 CD'));
    }

    public function test_extract_plate_finds_window_in_noisy_text(): void
    {
        $noisy = 'NOISYHEADER B 1234 CD NOISYTRAILER';
        $this->assertSame('B 1234 CD', $this->normalizer->extractPlate($noisy));
    }

    public function test_extract_plate_returns_null_for_empty_string(): void
    {
        $this->assertNull($this->normalizer->extractPlate(''));
    }

    public function test_extract_plate_returns_null_when_nothing_matches(): void
    {
        $this->assertNull($this->normalizer->extractPlate('JUST WORDS HERE'));
    }

    public function test_deprecated_alias_still_works(): void
    {
        $this->assertTrue($this->normalizer->matchesIndonesianFormat('B 1234 CD'));
        $this->assertFalse($this->normalizer->matchesIndonesianFormat('XX'));
    }
}
