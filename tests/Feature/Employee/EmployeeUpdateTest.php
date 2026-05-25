<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_authenticated_user_can_update_an_employee(): void
    {
        $employee = Employee::factory()->create(['name' => 'Old Name', 'currency' => 'USD']);

        $this->actingAs($this->user)
            ->putJson("/employees/api/employees/{$employee->id}", [
                'name'      => 'New Name',
                'birthdate' => $employee->birthdate,
                'sex'       => $employee->sex,
                'address'   => $employee->address,
                'salary'    => $employee->salary,
                'currency'  => 'JPY',
                'is_active' => $employee->is_active,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.currency', 'JPY');

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name' => 'New Name', 'currency' => 'JPY']);
    }

    public function test_nik_cannot_be_changed_on_update(): void
    {
        $employee = Employee::factory()->create(['nik' => '1234567890']);

        $this->actingAs($this->user)
            ->putJson("/employees/api/employees/{$employee->id}", [
                'name'      => $employee->name,
                'birthdate' => $employee->birthdate,
                'sex'       => $employee->sex,
                'address'   => $employee->address,
                'salary'    => $employee->salary,
                'is_active' => $employee->is_active,
                'nik'       => '9999999999',
            ])
            ->assertOk();

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'nik' => '1234567890']);
    }
}
