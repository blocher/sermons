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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('handle', 256)->nullable();
            $table->string('name', 256)->nullable();
            $table->string('rank', 256)->nullable();
            $table->unsignedSmallInteger('priority')->nullable();
            $table->string('color', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
