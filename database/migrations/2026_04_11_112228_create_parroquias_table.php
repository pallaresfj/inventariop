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
        Schema::create('parroquias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arciprestazgo_id')->index();
            $table->string('nombre', 80);
            $table->string('legacy_login')->nullable()->unique();
            $table->string('correo', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('direccion', 120)->nullable();
            $table->string('telefono', 30)->nullable()->index();
            $table->string('web', 120)->nullable();
            $table->string('imagen_path')->nullable();
            $table->timestamps();

            $table->unique(['arciprestazgo_id', 'nombre'], 'parroquias_arciprestazgo_nombre_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parroquias');
    }
};
