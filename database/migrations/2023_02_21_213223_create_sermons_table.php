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
        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title', 256)->nullable();
            $table->date('delivered_on')->nullable();
            $table->bigInteger('location_id')->nullable()->unsigned();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
            $table->bigInteger('feast_id')->nullable()->unsigned();
            $table->foreign('feast_id')->references('id')->on('holidays')->onDelete('set null');
            $table->integer('proper')->unsigned()->nullable();
            $table->string('mass_year', 1)->nullable();
            $table->text('sermon_summary')->nullable();
            $table->text('sermon_text')->nullable();
            $table->text('sermon_markup')->nullable();
            $table->string('file_name', 256)->nullable();
            $table->string('file', 256)->nullable();
            $table->text('readings')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermons');
    }
};
