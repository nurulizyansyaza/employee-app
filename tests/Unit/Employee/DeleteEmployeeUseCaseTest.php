<?php

declare(strict_types=1);

namespace Tests\Unit\Employee;

use App\Application\Employee\DeleteEmployeeUseCase;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Domain\Employee\Exceptions\EmployeeNotFoundException;
use App\Models\Employee;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteEmployeeUseCaseTest extends TestCase
{
    private MockInterface $repository;
    private DeleteEmployeeUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(EmployeeRepositoryInterface::class);
        $this->useCase = new DeleteEmployeeUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function given_existing_id_it_deletes_and_returns_employee_name(): void
    {
        $employee = new Employee(['name' => 'Alice']);

        $this->repository
            ->shouldReceive('findById')->once()->with('202401-001')->andReturn($employee);

        $this->repository
            ->shouldReceive('delete')->once()->with($employee);

        $name = $this->useCase->execute('202401-001');

        $this->assertSame('Alice', $name);
    }

    #[Test]
    public function given_nonexistent_id_it_throws_employee_not_found_exception(): void
    {
        $this->expectException(EmployeeNotFoundException::class);

        $this->repository
            ->shouldReceive('findById')->once()->with('GHOST')->andReturn(null);

        $this->useCase->execute('GHOST');
    }
}
