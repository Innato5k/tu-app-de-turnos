<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'start_time',
        'end_time',
        'status', 
        'capacity',
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

   
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'available_slot_id');
    }

    
}
