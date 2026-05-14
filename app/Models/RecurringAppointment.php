<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringAppointment extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'rrule',
        'start_date',
        'end_date',
        'cost',
        'modality',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Relación con el profesional
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el paciente
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación con todos los turnos individuales generados por esta regla
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'recurring_appointment_id');
    }
}