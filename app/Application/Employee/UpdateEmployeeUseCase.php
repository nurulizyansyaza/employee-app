<?php

declare(strict_types=1);

namespace App\Application\Employee;

use App\Application\Employee\DTO\UpdateEmployeeData;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Domain\Employee\Exceptions\EmployeeNotFoundException;
use App\Models\Employee;

final class UpdateEmployeeUseCase
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(string $id, UpdateEmployeeData $data): Employee
    {
        $employee = $this->repository->findById($id);

        if ($employee === null) {
            throw new EmployeeNotFoundException($id);
        }

        $payload = [
            'name'      => $data->name,
            'birthdate' => $data->birthdate,
            'sex'       => $data->sex,
            'salary'    => $data->salary,
            'address'   => $data->address,
        ];

        if ($data->currency !== null) {
            $payload['currency'] = $data->currency;
        }

        if ($data->isActive !== null) {
            $payload['is_active'] = $data->isActive;
        }

        return $this->repository->update($employee, $payload);
    }
}
