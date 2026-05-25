<?php

declare(strict_types=1);

namespace App\Domain\Employee;

use App\Application\Employee\DTO\EmployeeListCriteria;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeRepositoryInterface
{
    public function findById(string $id): ?Employee;

    public function paginate(EmployeeListCriteria $criteria): LengthAwarePaginator;

    public function create(array $data): Employee;

    public function update(Employee $employee, array $data): Employee;

    public function delete(Employee $employee): void;
}
