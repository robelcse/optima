<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socialuser extends Model
{
    use HasFactory;
    protected $fillable = [
        'media',
        'social_id',
        'user_name',
        'name',
        'email',
        'avatar',
        'access_token',
        'auth_id'
    ];
}
