<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalSchedule extends Model
{
    use HasFactory,  SoftDeletes;
    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'effective_start_date',
        'effective_end_date',
        'slot_duration',
        'observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'string', 
        'end_time' => 'string',   
        'effective_start_date' => 'date', 
        'effective_end_date' => 'date',  
    ];

    /**
     * Get the professional that owns the schedule.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
