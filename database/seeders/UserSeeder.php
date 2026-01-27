<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Desactivar foreign keys para truncar sin error
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Usuario Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0000000000',
            'address' => 'calle 123',
            'status'   => 'regular',
        ]);

        // Usuario normal
        User::create([
            'name' => 'Usuario Normal',
            'email' => 'usuario@test.com',
            'password' => Hash::make('password'),
            'role' => 'usuario',
            'phone' => '0000000002',
            'address' => 'calle 1234',
            'status'   => 'regular',
        ]);
    }
}
