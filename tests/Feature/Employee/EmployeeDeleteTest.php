<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_authenticated_user_can_delete_an_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->user)
            ->deleteJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_deleting_nonexistent_employee_returns_404(): void
    {
        $this->actingAs($this->user)
            ->deleteJson('/api/employees/NONEXISTENT')
            ->assertNotFound();
    }
}
