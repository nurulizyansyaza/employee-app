<?php

declare(strict_types=1);

namespace App\Application\Employee;

use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Domain\Employee\Exceptions\EmployeeNotFoundException;

final class DeleteEmployeeUseCase
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(string $id): string
    {
        $employee = $this->repository->findById($id);

        if ($employee === null) {
            throw new EmployeeNotFoundException($id);
        }

        $name = $employee->name;
        $this->repository->delete($employee);

        return $name;
    }
}
