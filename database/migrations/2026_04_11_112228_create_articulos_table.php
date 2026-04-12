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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parroquia_id')->index();
            $table->unsignedBigInteger('comunidad_id')->index();
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->enum('estado', ['B', 'M', 'R'])->default('B')->index();
            $table->unsignedInteger('precio')->default(0);
            $table->date('fecha_adquisicion')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();

            $table->index(['parroquia_id', 'comunidad_id'], 'articulos_parroquia_comunidad_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
