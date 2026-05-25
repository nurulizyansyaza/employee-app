<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ocr\PlateNormalizer;
use App\Services\Ocr\PlateOcrDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OcrController extends Controller
{
    /**
     * POST /api/ocr/plate
     */
    public function plate(Request $request, PlateOcrDriver $driver, PlateNormalizer $normalizer): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ]);

        try {
            $result = $driver->read($request->file('image')->getRealPath());
        } catch (Throwable $e) {
            Log::error('OCR driver failed', [
                'provider' => $driver->providerName(),
                'message'  => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'OCR provider error.',
                'error'   => $e->getMessage(),
            ], 502);
        }

        $rawText    = (string) ($result['raw_text'] ?? '');
        $normalized = $normalizer->normalize($rawText);
        $plate      = $normalizer->extractPlate($normalized);

        if ($plate === null) {
            return response()->json([
                'message'    => 'Plate could not be determined from the image.',
                'raw_text'   => $rawText,
                'normalized' => $normalized,
                'provider'   => $driver->providerName(),
            ], 422);
        }

        return response()->json([
            'plate_text'     => $plate,
            'matches_format' => $normalizer->matchesFormat($plate),
            'confidence'     => $result['confidence'] ?? null,
            'raw_text'       => $rawText,
            'provider'       => $driver->providerName(),
        ]);
    }
}
