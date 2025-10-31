<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('deal_item')->nullable()->after('follow_up_date'); // Adjust position if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('deal_item');
        });
    }
};
