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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('keyword', 256)->nullable();
            $table->string('title', 512)->nullable();
            $table->string('alt', 256)->nullable();
            $table->string('original_url', 512)->nullable();
            $table->string('filetype', 16)->nullable();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('height')->unsigned()->nullable();
            $table->string('source', 512)->nullable();
            $table->string('domain', 256)->nullable();
            $table->string('original_thumbnail', 512)->nullable();
            $table->string('file', 512)->nullable();
            $table->string('filename', 512)->nullable();
            $table->integer('sermon_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
