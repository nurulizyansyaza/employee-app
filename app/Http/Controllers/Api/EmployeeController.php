<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Employee\CreateEmployeeUseCase;
use App\Application\Employee\DeleteEmployeeUseCase;
use App\Application\Employee\DTO\CreateEmployeeData;
use App\Application\Employee\DTO\EmployeeListCriteria;
use App\Application\Employee\DTO\UpdateEmployeeData;
use App\Application\Employee\GetEmployeeUseCase;
use App\Application\Employee\ListEmployeesUseCase;
use App\Application\Employee\UpdateEmployeeUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly ListEmployeesUseCase $listEmployees,
        private readonly GetEmployeeUseCase $getEmployee,
        private readonly CreateEmployeeUseCase $createEmployee,
        private readonly UpdateEmployeeUseCase $updateEmployee,
        private readonly DeleteEmployeeUseCase $deleteEmployee,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->input('per_page', 10), 100));
        $direction = strtolower((string) $request->input('direction')) === 'desc' ? 'desc' : 'asc';
        $sort = (string) $request->input('sort', 'name');
        $search = trim((string) $request->input('search', ''));

        $criteria = new EmployeeListCriteria(
            perPage: $perPage,
            sort: $sort,
            direction: $direction,
            search: $search,
        );

        $paginator = $this->listEmployees->execute($criteria);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Employee $e) => EmployeeResource::make($e)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
                'sort'         => $sort,
                'direction'    => $direction,
                'search'       => $search,
            ],
        ]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->createEmployee->execute(
            CreateEmployeeData::fromArray($request->validated()),
        );

        return response()->json([
            'data'    => EmployeeResource::make($employee->fresh()),
            'message' => "Employee {$employee->name} has been added.",
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = $this->getEmployee->execute($id);

        return response()->json(['data' => EmployeeResource::make($employee)]);
    }

    public function update(UpdateEmployeeRequest $request, string $id): JsonResponse
    {
        $employee = $this->updateEmployee->execute(
            $id,
            UpdateEmployeeData::fromArray($request->validated()),
        );

        return response()->json([
            'data'    => EmployeeResource::make($employee),
            'message' => "Employee {$employee->name} has been updated.",
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $name = $this->deleteEmployee->execute($id);

        return response()->json([
            'message' => "Employee {$name} has been deleted.",
        ]);
    }
}
