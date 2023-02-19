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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 256);
            $table->string('subname', 256)->nullable();
            $table->string('street', 256)->nullable();
            $table->string('city', 256)->nullable();
            $table->string('state', 256)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('country', 256)->nullable();
            $table->string('website', 4096)->nullable();
            $table->string('diocese', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
