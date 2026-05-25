<?php

declare(strict_types=1);

namespace App\Application\Employee\DTO;

final class EmployeeListCriteria
{
    public function __construct(
        public readonly int $perPage = 10,
        public readonly string $sort = 'name',
        public readonly string $direction = 'asc',
        public readonly string $search = '',
    ) {}
}
