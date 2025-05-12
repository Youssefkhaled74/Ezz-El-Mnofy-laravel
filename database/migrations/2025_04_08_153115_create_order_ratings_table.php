<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('delivery_time')->nullable()->comment('Rating 1-5');
            $table->unsignedTinyInteger('delivery_service')->nullable()->comment('Rating 1-5');
            $table->unsignedTinyInteger('food_quality')->nullable()->comment('Rating 1-5');
            $table->unsignedTinyInteger('packing')->nullable()->comment('Rating 1-5');
            $table->unsignedTinyInteger('overall_experience')->nullable()->comment('Rating 1-5');
            $table->text('additional_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_ratings');
    }
};
