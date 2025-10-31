<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('balance_update_logs', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('new_value'); // Adding notes column
        });
    }

    public function down()
    {
        Schema::table('balance_update_logs', function (Blueprint $table) {
            $table->dropColumn('notes'); // Rollback by removing the column
        });
    }
};
