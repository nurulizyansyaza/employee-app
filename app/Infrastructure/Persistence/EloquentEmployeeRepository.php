<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Employee\DTO\EmployeeListCriteria;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentEmployeeRepository implements EmployeeRepositoryInterface
{
    private const SORTABLE = ['id', 'name', 'birthdate', 'sex', 'salary', 'nik', 'is_active'];

    public function findById(string $id): ?Employee
    {
        return Employee::find($id);
    }

    public function paginate(EmployeeListCriteria $criteria): LengthAwarePaginator
    {
        $sort = in_array($criteria->sort, self::SORTABLE, true) ? $criteria->sort : 'name';
        $direction = $criteria->direction === 'desc' ? 'desc' : 'asc';

        $query = Employee::query();

        if ($criteria->search !== '') {
            $like = '%' . $criteria->search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('nik', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('id', 'like', $like);
            });
        }

        return $query->orderBy($sort, $direction)->paginate($criteria->perPage);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->fill($data)->save();

        return $employee->fresh();
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
