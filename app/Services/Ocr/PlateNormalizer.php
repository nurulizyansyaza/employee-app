<?php

namespace App\Services\Ocr;

class PlateNormalizer
{
    /**
     * Generic license-plate pattern (Latin-alphabet, country-agnostic).
     *
     * Accepts plates that:
     *  - are 2–15 characters long after normalization,
     *  - consist of uppercase letters, digits, single spaces, and hyphens,
     *  - start and end with an alphanumeric character,
     *  - contain at least one letter AND at least one digit.
     *
     * Examples it accepts:
     *   "B 1234 CD"   (Indonesia)
     *   "ABC 1234"    (US-style)
     *   "AB12 CDE"    (UK)
     *   "M-AB 1234"   (Germany)
     *   "7XYZ123"     (California)
     */
    public const PATTERN = '/^(?=.*[A-Z])(?=.*\d)[A-Z0-9](?:[A-Z0-9 \-]{0,13}[A-Z0-9])?$/';

    /**
     * Apply normalization:
     *  - Uppercase
     *  - Strip everything except [A-Z0-9], spaces, and hyphens
     *  - Collapse repeated spaces
     */
    public function normalize(string $text): string
    {
        $upper   = mb_strtoupper($text);
        $cleaned = preg_replace('/[^A-Z0-9\s\-]/', ' ', $upper) ?? '';
        $single  = preg_replace('/\s+/', ' ', $cleaned) ?? '';

        return trim($single);
    }

    /**
     * Pull the best-looking plate substring out of a normalized string.
     *
     *  1. If the whole normalized string already matches, use it.
     *  2. Otherwise try every contiguous window of 1–4 whitespace-separated
     *     tokens, picking the longest window that matches.
     */
    public function extractPlate(string $normalized): ?string
    {
        if ($normalized === '') {
            return null;
        }

        if ($this->matchesFormat($normalized)) {
            return $normalized;
        }

        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $best   = null;

        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            for ($len = 1; $len <= 4 && $i + $len <= $count; $len++) {
                $candidate = implode(' ', array_slice($tokens, $i, $len));
                if ($this->matchesFormat($candidate)) {
                    if ($best === null || strlen($candidate) > strlen($best)) {
                        $best = $candidate;
                    }
                }
            }
        }

        return $best;
    }

    /**
     * Country-agnostic format check.
     */
    public function matchesFormat(string $plate): bool
    {
        return (bool) preg_match(self::PATTERN, $plate);
    }

    /**
     * Backwards-compatible alias.
     *
     * @deprecated Use matchesFormat() instead.
     */
    public function matchesIndonesianFormat(string $plate): bool
    {
        return $this->matchesFormat($plate);
    }
}
