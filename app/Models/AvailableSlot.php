<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableSlot extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'status',
        'capacity',
        'notes',
        'appointment_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
