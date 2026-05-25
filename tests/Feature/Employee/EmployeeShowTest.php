<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_authenticated_user_can_view_a_single_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/employees/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $employee->id);
    }

    public function test_viewing_nonexistent_employee_returns_404(): void
    {
        $this->actingAs($this->user)
            ->getJson('/employees/api/employees/NONEXISTENT')
            ->assertNotFound();
    }
}
