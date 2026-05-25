<?php

declare(strict_types=1);

namespace Tests\Unit\Employee;

use App\Application\Employee\DTO\EmployeeListCriteria;
use App\Application\Employee\ListEmployeesUseCase;
use App\Domain\Employee\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListEmployeesUseCaseTest extends TestCase
{
    private MockInterface $repository;
    private ListEmployeesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(EmployeeRepositoryInterface::class);
        $this->useCase = new ListEmployeesUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function given_criteria_it_delegates_to_repository_paginate(): void
    {
        $criteria = new EmployeeListCriteria(perPage: 5, sort: 'name', direction: 'asc', search: 'Alice');
        $paginator = new LengthAwarePaginator([], 0, 5);

        $this->repository
            ->shouldReceive('paginate')
            ->once()
            ->with($criteria)
            ->andReturn($paginator);

        $result = $this->useCase->execute($criteria);

        $this->assertSame($paginator, $result);
    }
}
