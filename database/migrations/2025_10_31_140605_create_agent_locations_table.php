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
        Schema::create('agent_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id'); // assumes you have agents table
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('accuracy')->nullable(); // optional: accuracy in meters
            $table->timestamp('location_time')->useCurrent();
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
            $table->index('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_locations');
    }
};
