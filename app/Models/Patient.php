<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // IMPORTANTE
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes; // Habilitamos SoftDeletes

    protected $fillable = [
        'name',
        'last_name',
        'cuil',
        'birth_date',
        'gender',
        'medical_history',
        'email',
        'phone',
        'phone_opt',
        'address',
        'city',
        'province',
        'postal_code',
        'medical_coverage',
        'affiliate_number',
        'preferred_modality',
        'preferred_cost',
        'observations',
        'institution_id',
        'created_by_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'institution_id' => 'integer',
        'created_by_id' => 'integer',
        'deleted_at' => 'datetime', 
    ];
}