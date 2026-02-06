<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('provider_name');
            $table->string('frequency'); // e.g. Weekly, Monthly
            $table->string('day_of_week')->nullable();
            $table->string('time_slot')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_number')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_services');
    }
};
