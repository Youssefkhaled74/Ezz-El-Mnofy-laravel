<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Chef extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'chefs';
    protected $fillable = ['name', 'email', 'password', 'branch_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}