<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@email.com',
                'email_verified_at' => '2024-05-12 16:45:04',
                'password' => Hash::make('admin123'),
            ]
        ];

        foreach($users as $user) {
            $uuid = Str::uuid();

            while(DB::table('users')->where('uuid',$uuid)->first()) {
                $uuid = Str::uuid();
            }

            DB::table('users')->insert([
                'uuid' => $uuid,
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => $user['email_verified_at'],
                'password' => $user['password']
            ]);
        }
    }
}