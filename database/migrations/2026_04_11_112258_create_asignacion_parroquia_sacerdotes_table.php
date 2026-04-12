<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asignacion_parroquia_sacerdotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parroquia_id')->index();
            $table->unsignedBigInteger('sacerdote_id')->index();
            $table->unsignedBigInteger('cargo_parroquial_id')->nullable()->index();
            $table->boolean('vigente')->default(true)->index();
            $table->timestamps();

            $table->unique(
                ['parroquia_id', 'sacerdote_id', 'cargo_parroquial_id'],
                'asig_parroquia_sacerdote_cargo_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_parroquia_sacerdotes');
    }
};
