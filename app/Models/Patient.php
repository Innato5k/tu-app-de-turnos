<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
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
    ];
}
