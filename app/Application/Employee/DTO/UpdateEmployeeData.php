<?php

declare(strict_types=1);

namespace App\Application\Employee\DTO;

final class UpdateEmployeeData
{
    public function __construct(
        public readonly string $name,
        public readonly string $birthdate,
        public readonly bool $sex,
        public readonly float $salary,
        public readonly ?string $address = null,
        public readonly ?string $currency = null,
        public readonly ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            birthdate: $data['birthdate'],
            sex: (bool) $data['sex'],
            salary: (float) $data['salary'],
            address: $data['address'] ?? null,
            currency: isset($data['currency']) ? strtoupper($data['currency']) : null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
        );
    }
}
