<?php

declare(strict_types=1);

namespace Tests\Unit\Employee;

use App\Application\Employee\GetEmployeeUseCase;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Domain\Employee\Exceptions\EmployeeNotFoundException;
use App\Models\Employee;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetEmployeeUseCaseTest extends TestCase
{
    private MockInterface $repository;
    private GetEmployeeUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(EmployeeRepositoryInterface::class);
        $this->useCase = new GetEmployeeUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function given_existing_id_it_returns_the_employee(): void
    {
        $employee = new Employee(['name' => 'Alice']);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('202401-001')
            ->andReturn($employee);

        $result = $this->useCase->execute('202401-001');

        $this->assertSame($employee, $result);
    }

    #[Test]
    public function given_nonexistent_id_it_throws_employee_not_found_exception(): void
    {
        $this->expectException(EmployeeNotFoundException::class);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('NONEXISTENT')
            ->andReturn(null);

        $this->useCase->execute('NONEXISTENT');
    }
}
