<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRememberTokenToAgentsTable extends Migration
{
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->rememberToken(); // Adds a nullable string column named 'remember_token'
        });
    }

    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('remember_token'); // Drops the 'remember_token' column
        });
    }
}
