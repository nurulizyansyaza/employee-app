<?php

declare(strict_types=1);

namespace App\Application\Employee;

use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Domain\Employee\Exceptions\EmployeeNotFoundException;
use App\Models\Employee;

final class GetEmployeeUseCase
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(string $id): Employee
    {
        $employee = $this->repository->findById($id);

        if ($employee === null) {
            throw new EmployeeNotFoundException($id);
        }

        return $employee;
    }
}
