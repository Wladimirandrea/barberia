<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tabla users (Dueño del horario)
            // Usamos foreignId para integridad referencial
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Campos de tiempo sugeridos para FullCalendar
            $table->date('date');        // Ejemplo: 2025-12-21
            $table->time('start_time');  // Ejemplo: 08:00:00
            $table->time('end_time');    // Ejemplo: 09:00:00

            // Estado del slot (disponible o reservado)
            $table->boolean('is_available')->default(true);
            $table->boolean('is_blocked')->default(false);

            $table->timestamps();
            
            // Índice para mejorar el rendimiento de las búsquedas por fecha y usuario
            $table->index(['date', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
