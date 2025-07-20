<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory,SoftDeletes;
       /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
            'name',
            'last_name',
            'cuil',
            'email',
            'phone',
            'phone_opt',
            'observations',
            'birth_date',
            'gender',
            'address',
            'city',
            'province',
            'postal_code',
            'medical_coerage',
            'is_active',
            'is_deleted',
    ];
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Si tienes esta columna
        'is_active' => 'boolean', // ¡Añade o confirma esta línea!
    ];

}
