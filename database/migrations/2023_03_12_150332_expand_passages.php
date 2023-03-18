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
        Schema::table('readings', function (Blueprint $table) {
            $table->string('book', 256)->nullable();
            $table->smallInteger('start_chapter')->nullable()->unsigned();
            $table->smallInteger('start_verse')->nullable()->unsigned();
            $table->smallInteger('end_chapter')->nullable()->unsigned();
            $table->smallInteger('end_verse')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
