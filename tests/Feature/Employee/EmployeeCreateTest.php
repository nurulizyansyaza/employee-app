<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_authenticated_user_can_create_an_employee(): void
    {
        $payload = [
            'name'      => 'John Doe',
            'birthdate' => '1990-01-15',
            'sex'       => true,
            'address'   => '123 Main St',
            'salary'    => 5000000,
            'currency'  => 'EUR',
            'nik'       => '1234567890',
            'is_active' => true,
        ];

        $this->actingAs($this->user)
            ->postJson('/employees/api/employees', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.nik', '1234567890')
            ->assertJsonPath('data.currency', 'EUR');

        $this->assertDatabaseHas('employees', ['name' => 'John Doe', 'currency' => 'EUR']);
    }

    public function test_currency_defaults_to_usd_when_omitted(): void
    {
        $payload = [
            'name'      => 'Jane Doe',
            'birthdate' => '1992-05-20',
            'sex'       => false,
            'address'   => '456 Side St',
            'salary'    => 4500,
            'nik'       => '1111111111',
            'is_active' => true,
        ];

        $this->actingAs($this->user)
            ->postJson('/employees/api/employees', $payload)
            ->assertCreated()
            ->assertJsonPath('data.currency', 'USD');

        $this->assertDatabaseHas('employees', ['nik' => '1111111111', 'currency' => 'USD']);
    }

    public function test_invalid_currency_code_returns_422(): void
    {
        $this->actingAs($this->user)
            ->postJson('/employees/api/employees', [
                'name'      => 'Bad Currency',
                'birthdate' => '1990-01-01',
                'sex'       => true,
                'salary'    => 1000,
                'currency'  => 'US1',
                'nik'       => '2222222222',
                'is_active' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_creating_employee_with_missing_required_fields_returns_422(): void
    {
        $this->actingAs($this->user)
            ->postJson('/employees/api/employees', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'birthdate', 'salary', 'nik']);
    }

    public function test_creating_employee_with_duplicate_nik_returns_422(): void
    {
        Employee::factory()->create(['nik' => '9999999999']);

        $this->actingAs($this->user)
            ->postJson('/employees/api/employees', [
                'name'      => 'Duplicate NIK',
                'birthdate' => '1990-01-01',
                'sex'       => true,
                'address'   => 'Somewhere',
                'salary'    => 3000000,
                'nik'       => '9999999999',
                'is_active' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nik']);
    }
}
