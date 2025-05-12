<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_phone_number')->nullable();
            $table->string('referral_code')->nullable();
            $table->date('birthday')->nullable();
            $table->string('whatsapp_country_code')->nullable();
            $table->string('discount_id_photo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_phone_number');
            $table->dropColumn('referral_code');
            $table->dropColumn('birthday');
            $table->dropColumn('whatsapp_country_code');
            $table->dropColumn('discount_id_photo');
        });
    }
};
