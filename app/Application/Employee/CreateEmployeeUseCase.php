<?php

declare(strict_types=1);

namespace App\Application\Employee;

use App\Application\Employee\DTO\CreateEmployeeData;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Models\Employee;

final class CreateEmployeeUseCase
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(CreateEmployeeData $data): Employee
    {
        return $this->repository->create([
            'name'      => $data->name,
            'birthdate' => $data->birthdate,
            'sex'       => $data->sex,
            'salary'    => $data->salary,
            'nik'       => $data->nik,
            'address'   => $data->address,
            'currency'  => $data->currency,
            'is_active' => $data->isActive,
        ]);
    }
}
