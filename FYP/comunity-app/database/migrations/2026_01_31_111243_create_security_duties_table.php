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
        Schema::create('security_duties', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('shift'); // Morning, Evening, Night
            $table->string('guard_name');
            $table->string('location')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_duties');
    }
};
