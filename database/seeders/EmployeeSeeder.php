<?php

namespace Database\Seeders;

use App\Models\Employee;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed a modest, demoable set of employees.
     *
     * Or call directly from tinker:
     *   \Database\Seeders\EmployeeSeeder::seedLarge(100000);
     */
    public function run(): void
    {
        $count = (int) env('EMPLOYEE_SEED_COUNT', 25);

        if ($count < 500) {
            Employee::factory()->count($count)->create();
        } else {
            static::seedLarge($count);
        }
    }

    /**
     * Fast bulk seeder bypassing model events, generates IDs directly.
     */
    public static function seedLarge(int $total = 100_000, int $chunk = 1_000): void
    {
        $faker      = FakerFactory::create();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'IDR', 'SGD', 'MYR', 'INR', 'AUD', 'CNY'];
        $now        = Carbon::now()->toDateTimeString();

        // ---- ID generation ------------------------------------------------
        $currentDate = Carbon::now();
        $prefix      = $currentDate->format('Ym');
        $counter     = (int) substr(
            Employee::query()
                ->where('id', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->value('id') ?? ($prefix . '00000'),
            6
        );

        // ---- NIK generation -----------------------------------------------
        $nikCounter = max((int) Employee::query()->max('nik'), 1_000_000_000);

        for ($i = 0; $i < $total; $i += $chunk) {
            $rows  = [];
            $batch = min($chunk, $total - $i);

            for ($j = 0; $j < $batch; $j++) {
                $counter++;
                $nikCounter++;

                if ($counter > 99_999) {
                    $currentDate->addMonth();
                    $prefix  = $currentDate->format('Ym');
                    $counter = 1;
                }

                $rows[] = [
                    'id'          => $prefix . str_pad((string) $counter, 5, '0', STR_PAD_LEFT),
                    'name'        => substr($faker->name(), 0, 30),
                    'birthdate'   => $faker->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
                    'sex'         => (int) $faker->boolean(),
                    'address'     => substr($faker->address(), 0, 200),
                    'currency'    => $currencies[array_rand($currencies)],
                    'salary'      => $faker->numberBetween(2_000, 80_000_000),
                    'nik'         => str_pad((string) $nikCounter, 10, '0', STR_PAD_LEFT),
                    'is_active'   => (int) $faker->boolean(85),
                    'entry_date'  => $now,
                    'update_date' => $now,
                ];
            }

            DB::table('employees')->insert($rows);
        }
    }
}
