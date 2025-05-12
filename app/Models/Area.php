<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $table = 'areas';
    protected $fillable = ['branch_id', 'name', 'points', 'delivery_fees', 'is_active'];
    protected $casts = [
        'id' => 'integer',
        'branch_id' => 'integer',
        'name' => 'string',
        'points' => 'json',
        'delivery_fees' => 'decimal:2', // Updated to decimal:2
        'is_active' => 'string',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}