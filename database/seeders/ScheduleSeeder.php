<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buscamos el primer usuario administrador o el que tengas logueado
        // Si no tienes usuarios, asegúrate de crear uno primero.
        $user = User::first();

        if (!$user) {
            $this->command->info('No hay usuarios en la base de datos. Crea uno antes de correr este seeder.');
            return;
        }

        // 2. Definimos una fecha base (hoy)
        $today = Carbon::today();

        // 3. Creamos 5 slots de prueba para esta semana
        $slots = [
            ['start' => '09:00:00', 'end' => '09:30:00', 'available' => true],
            ['start' => '09:30:00', 'end' => '10:00:00', 'available' => false],
            ['start' => '10:00:00', 'end' => '10:30:00', 'available' => true],
            ['start' => '10:30:00', 'end' => '11:00:00', 'available' => true],
            ['start' => '11:00:00', 'end' => '11:30:00', 'available' => false],
        ];

        foreach ($slots as $index => $slot) {
            Schedule::create([
                'user_id'      => $user->id,
                'date'         => $today->copy()->addDays(rand(0, 3))->format('Y-m-d'), // Días aleatorios
                'start_time'   => $slot['start'],
                'end_time'     => $slot['end'],
                'is_available' => $slot['available'],
            ]);
        }

        $this->command->info('¡Seeder ejecutado! Se crearon 5 horarios de prueba.');
    }
}
