<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    private const SORTABLE = ['id', 'name', 'birthdate', 'sex', 'salary', 'nik', 'is_active'];

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $sort = in_array($request->input('sort'), self::SORTABLE, true)
            ? $request->input('sort')
            : 'name';

        $direction = strtolower((string) $request->input('direction')) === 'desc' ? 'desc' : 'asc';

        $search = trim((string) $request->input('search', ''));

        $query = Employee::query();

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('nik', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('id', 'like', $like);
            });
        }

        $paginator = $query->orderBy($sort, $direction)->paginate($perPage);

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
        $employee = Employee::create($request->validated());

        return response()->json([
            'data'    => EmployeeResource::make($employee->fresh()),
            'message' => "Employee {$employee->name} has been added.",
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        return response()->json(['data' => EmployeeResource::make($employee)]);
    }

    public function update(UpdateEmployeeRequest $request, string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->fill($request->validated())->save();

        return response()->json([
            'data'    => EmployeeResource::make($employee->fresh()),
            'message' => "Employee {$employee->name} has been updated.",
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $name = $employee->name;
        $employee->delete();

        return response()->json([
            'message' => "Employee {$name} has been deleted.",
        ]);
    }
}
