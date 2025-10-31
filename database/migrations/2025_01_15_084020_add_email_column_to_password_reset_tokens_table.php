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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->after('username')->index(); // Add email column with an index
        });
    }

    public function down()
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('email'); // Remove email column
        });
    }
};
