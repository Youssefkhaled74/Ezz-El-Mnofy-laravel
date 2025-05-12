<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = "branches";
    protected $fillable = ['name', 'email', 'phone', 'latitude', 'longitude', 'city', 'state', 'zip_code', 'address', 'status', 'brand_id'];
    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'email'     => 'string',
        'phone'     => 'string',
        'latitude'  => 'string',
        'longitude' => 'string',
        'city'      => 'string',
        'state'     => 'string',
        'zip_code'  => 'string',
        'address'   => 'string',
        'status'    => 'integer',
        'brand_id'  => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function items()
    {
        return $this->belongsToMany(Item::class, 'branch_item');
    }
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
