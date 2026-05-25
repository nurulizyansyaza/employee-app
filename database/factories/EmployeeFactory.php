<?php

namespace Database\Factories;

use App\Models\Employee;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    protected function withFaker(): Generator
    {
        return FakerFactory::create(config('app.faker_locale', 'en_US'));
    }

    public function definition(): array
    {
        $now = Carbon::now();

        return [
            'name'        => substr($this->faker->name(), 0, 30),
            'birthdate'   => $this->faker->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
            'sex'         => $this->faker->boolean(),
            'address'     => substr($this->faker->address(), 0, 200),
            'currency'    => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'JPY', 'IDR', 'SGD', 'MYR', 'INR', 'AUD', 'CNY']),
            'salary'      => $this->faker->numberBetween(2_000, 80_000_000),
            'nik'         => (string) $this->faker->unique()->numerify('##########'),
            'is_active'   => $this->faker->boolean(85),
            'entry_date'  => $now,
            'update_date' => $now,
        ];
    }
}
