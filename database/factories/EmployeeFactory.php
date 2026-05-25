<?php

namespace Database\Factories;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $now = Carbon::now();

        return [
            'name'        => substr(fake()->name(), 0, 30),
            'birthdate'   => fake()->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
            'sex'         => fake()->boolean(),
            'address'     => substr(fake()->address(), 0, 200),
            'currency'    => fake()->randomElement(['USD', 'EUR', 'GBP', 'JPY', 'IDR', 'SGD', 'MYR', 'INR', 'AUD', 'CNY']),
            'salary'      => fake()->numberBetween(2_000, 80_000_000),
            'nik'         => (string) fake()->unique()->numerify('##########'),
            'is_active'   => fake()->boolean(85),
            'entry_date'  => $now,
            'update_date' => $now,
        ];
    }
}
