<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branch_item', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('item_id');
        });
    }

    public function down()
    {
        Schema::table('branch_item', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
