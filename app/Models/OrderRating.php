<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OrderRating extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'order_ratings';

    protected $fillable = [
        'order_id',
        'user_id',
        'delivery_time',
        'delivery_service',
        'food_quality',
        'packing',
        'overall_experience',
        'additional_note',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'user_id' => 'integer',
        'delivery_time' => 'integer',
        'delivery_service' => 'integer',
        'food_quality' => 'integer',
        'packing' => 'integer',
        'overall_experience' => 'integer',
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rating_photo')
             ->singleFile()
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
    }
}