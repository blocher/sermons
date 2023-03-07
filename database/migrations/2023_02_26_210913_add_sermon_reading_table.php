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
        Schema::create('sermon_reading', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('sermon_id')->unsigned();
            $table->foreign('sermon_id')->references('id')->on('sermons')->onDelete('cascade');
            $table->bigInteger('reading_id')->unsigned();
            $table->foreign('reading_id')->references('id')->on('readings')->onDelete('cascade');
            $table->smallInteger('order')->unsigned()->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_reading');
    }
};
