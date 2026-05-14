<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlackoutPeriod extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'reason',
        'is_all_day'
    ];

    // Casteamos las fechas para que Laravel las maneje como objetos Carbon automáticamente
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    /**
     * Relación con el profesional
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}