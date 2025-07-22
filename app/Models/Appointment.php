<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AvailableSlot;
use App\Models\Patient;
use App\Models\User;   

class Appointment extends Model
{
    use HasFactory ,  SoftDeletes;

    protected $fillable = [
        'patient_id',
        'user_id', // Para el profesional
        'available_slot_id',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user (professional) that owns the appointment.
     */
    public function user() // Relación con el profesional (User)
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the available slot that the appointment belongs to.
     */
    public function availableSlot()
    {
        return $this->belongsTo(AvailableSlot::class, 'available_slot_id');
    }
}
