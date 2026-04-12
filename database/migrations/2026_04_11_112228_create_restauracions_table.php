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
        Schema::create('restauraciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('articulo_id')->index();
            $table->date('fecha_restauracion');
            $table->unsignedInteger('costo_restauracion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->timestamps();

            $table->index(['articulo_id', 'fecha_restauracion'], 'restauraciones_articulo_fecha_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restauraciones');
    }
};
