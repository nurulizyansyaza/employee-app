<?php

namespace App\Http\Resources;

use App\Models\Employee;

final class EmployeeResource
{
    public static function make(Employee $e): array
    {
        $currency = strtoupper((string) ($e->currency ?? 'USD'));
        $salary   = (float) $e->salary;

        return [
            'id'               => $e->id,
            'name'             => $e->name,
            'birthdate'        => optional($e->birthdate)->toDateString(),
            'sex'              => (bool) $e->sex,
            'sex_label'        => $e->sex ? 'Male' : 'Female',
            'address'          => $e->address,
            'salary'           => $salary,
            'currency'         => $currency,
            'salary_formatted' => self::formatSalary($salary, $currency),
            'nik'              => $e->nik,
            'is_active'        => (bool) $e->is_active,
            'status_label'     => $e->is_active ? 'Active' : 'Inactive',
            'entry_date'       => optional($e->entry_date)->toIso8601String(),
            'update_date'      => optional($e->update_date)->toIso8601String(),
        ];
    }

    private static function formatSalary(float $amount, string $currency): string
    {
        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            $out = $fmt->formatCurrency($amount, $currency);
            if ($out !== false) {
                return $out;
            }
        }

        return $currency . ' ' . number_format($amount, 2, '.', ',');
    }
}
