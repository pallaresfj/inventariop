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
        Schema::create('arciprestazgos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->string('correo', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->unsignedBigInteger('arcipestre_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arciprestazgos');
    }
};
