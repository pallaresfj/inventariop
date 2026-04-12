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
        Schema::create('comunidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parroquia_id')->index();
            $table->string('nombre', 80);
            $table->string('legacy_login')->nullable()->unique();
            $table->string('correo', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('direccion', 120)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('imagen_path')->nullable();
            $table->timestamps();

            $table->unique(['parroquia_id', 'nombre'], 'comunidades_parroquia_nombre_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunidades');
    }
};
