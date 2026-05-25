<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_authenticated_user_can_list_employees(): void
    {
        Employee::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->getJson('/employees/api/employees')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'birthdate', 'sex', 'salary', 'currency', 'salary_formatted', 'nik', 'is_active']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_listing_supports_search_filter(): void
    {
        Employee::factory()->create(['name' => 'Alice Smith']);
        Employee::factory()->create(['name' => 'Bob Jones']);

        $response = $this->actingAs($this->user)
            ->getJson('/employees/api/employees?search=Alice')
            ->assertOk();

        $names = collect($response->json('data'))->pluck('name');
        $this->assertContains('Alice Smith', $names);
        $this->assertNotContains('Bob Jones', $names);
    }

    public function test_listing_supports_sorting(): void
    {
        Employee::factory()->create(['name' => 'Zara']);
        Employee::factory()->create(['name' => 'Aaron']);

        $response = $this->actingAs($this->user)
            ->getJson('/employees/api/employees?sort=name&direction=asc')
            ->assertOk();

        $names = collect($response->json('data'))->pluck('name')->values();
        $this->assertEquals('Aaron', $names->first());
    }
}
