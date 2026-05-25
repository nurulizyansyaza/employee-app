<?php

declare(strict_types=1);

namespace App\Application\Employee;

use App\Application\Employee\DTO\EmployeeListCriteria;
use App\Domain\Employee\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListEmployeesUseCase
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(EmployeeListCriteria $criteria): LengthAwarePaginator
    {
        return $this->repository->paginate($criteria);
    }
}
