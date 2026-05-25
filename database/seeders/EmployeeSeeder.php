<?php

namespace Database\Seeders;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed a modest, demoable set of employees.
     *
     * For a stress-test of 100k+ records, run:
     *   php artisan tinker
     *   >>> \Database\Seeders\EmployeeSeeder::seedLarge(100000);
     */
    public function run(): void
    {
        $count = (int) env('EMPLOYEE_SEED_COUNT', 25);

        for ($i = 0; $i < $count; $i++) {
            Employee::factory()->create();
        }
    }

    /**
     * Fast bulk seeder bypassing model events, generates IDs directly.
     */
    public static function seedLarge(int $total = 100_000, int $chunk = 1000): void
    {
        $factory = Employee::factory();
        $prefix  = Carbon::now()->format('Ym');
        $counter = (int) substr(Employee::query()
            ->where('id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('id') ?? ($prefix . '00000'), 6);

        for ($i = 0; $i < $total; $i += $chunk) {
            $rows = [];
            $batch = min($chunk, $total - $i);
            $now = Carbon::now();
            for ($j = 0; $j < $batch; $j++) {
                $counter++;
                $row = $factory->raw();
                $row['id']          = $prefix . str_pad((string) $counter, 5, '0', STR_PAD_LEFT);
                $row['nik']         = (string) random_int(1_000_000_000, 9_999_999_999);
                $row['entry_date']  = $now;
                $row['update_date'] = $now;
                $rows[] = $row;
            }
            DB::table('employees')->insert($rows);
        }
    }
}
