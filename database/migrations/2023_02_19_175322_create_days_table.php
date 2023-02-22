<?php

use App\Models\Holiday;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date')->unique();
            $table->foreignIdFor(Holiday::class, "holiday_1_id");
            $table->foreignIdFor(Holiday::class, "holiday_2_id")->nullable();
            $table->foreignIdFor(Holiday::class, "holiday_3_id")->nullable();
            $table->unsignedSmallInteger('proper',)->nullable();
            $table->string('mass_year', 1)->nullable();
            $table->unsignedSmallInteger('office_year',)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
