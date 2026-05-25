<?php

declare(strict_types=1);

namespace App\Application\Employee\DTO;

final class CreateEmployeeData
{
    public function __construct(
        public readonly string $name,
        public readonly string $birthdate,
        public readonly bool $sex,
        public readonly float $salary,
        public readonly string $nik,
        public readonly ?string $address = null,
        public readonly string $currency = 'USD',
        public readonly bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            birthdate: $data['birthdate'],
            sex: (bool) $data['sex'],
            salary: (float) $data['salary'],
            nik: $data['nik'],
            address: $data['address'] ?? null,
            currency: strtoupper($data['currency'] ?? 'USD'),
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }
}
