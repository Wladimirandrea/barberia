<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'is_available',
        'user_id'
    ];

    // Obligamos a Laravel a que trate estos campos como tipos específicos
    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_available' => 'boolean',
    ];

    /**
     * Los "Appends" incluyen estos campos calculados automáticamente 
     * cada vez que conviertas el modelo a JSON (lo que recibe Vue).
     */
    protected $appends = ['start', 'end', 'title', 'backgroundColor', 'borderColor'];

    /**
     * Une Fecha + Hora de Inicio para FullCalendar
     * Ejemplo: "2025-12-21T09:00:00"
     */
    public function getStartAttribute()
    {
        return $this->date->format('Y-m-d') . 'T' . $this->start_time;
    }

    /**
     * Une Fecha + Hora de Fin
     */
    public function getEndAttribute()
    {
        return $this->date->format('Y-m-d') . 'T' . $this->end_time;
    }

    /**
     * Define el título que se verá en el recuadro del calendario
     */
    public function getTitleAttribute()
    {
        return $this->is_available ? '✅ Disponible' : '🚫 Ocupado';
    }

    /**
     * Colores dinámicos según disponibilidad
     */
    public function getBackgroundColorAttribute()
    {
        return $this->is_available ? '#22c55e' : '#ef4444'; // Verde o Rojo (Tailwind colors)
    }

    public function getBorderColorAttribute()
    {
        return $this->is_available ? '#16a34a' : '#b91c1c';
    }

    /**
     * Relación: El horario pertenece a un usuario (Admin/Especialista)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
