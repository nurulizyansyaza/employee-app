<?php

declare(strict_types=1);

namespace Tests\Unit\Employee;

use App\Application\Employee\CreateEmployeeUseCase;
use App\Application\Employee\DTO\CreateEmployeeData;
use App\Domain\Employee\EmployeeRepositoryInterface;
use App\Models\Employee;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateEmployeeUseCaseTest extends TestCase
{
    private MockInterface $repository;
    private CreateEmployeeUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(EmployeeRepositoryInterface::class);
        $this->useCase = new CreateEmployeeUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function given_valid_data_when_creating_employee_then_repository_create_is_called(): void
    {
        $data = new CreateEmployeeData(
            name: 'Alice',
            birthdate: '1990-01-01',
            sex: true,
            salary: 5000.00,
            nik: '3201234567890001',
            address: 'Jakarta',
            currency: 'USD',
            isActive: true,
        );

        $employee = new Employee(['name' => 'Alice']);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name'      => 'Alice',
                'birthdate' => '1990-01-01',
                'sex'       => true,
                'salary'    => 5000.00,
                'nik'       => '3201234567890001',
                'address'   => 'Jakarta',
                'currency'  => 'USD',
                'is_active' => true,
            ])
            ->andReturn($employee);

        $result = $this->useCase->execute($data);

        $this->assertSame($employee, $result);
    }
}
