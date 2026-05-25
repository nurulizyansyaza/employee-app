<?php

namespace Tests\Feature\Employee;

use Tests\TestCase;

class EmployeeAuthTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_employee_api(): void
    {
        $this->getJson('/employees/api/employees')->assertUnauthorized();
    }
}
