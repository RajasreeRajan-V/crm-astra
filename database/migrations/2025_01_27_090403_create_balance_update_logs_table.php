<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('balance_update_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('field_updated'); // e.g., 'rate' or 'balance'
            $table->decimal('previous_value', 10, 2);
            $table->decimal('new_value', 10, 2);
            $table->unsignedBigInteger('updated_by')->nullable(); // Reference to the user who made the change
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('agents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_update_logs');
    }
};
