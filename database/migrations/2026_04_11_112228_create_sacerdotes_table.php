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
        Schema::create('sacerdotes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->unsignedBigInteger('titulo_sacerdotal_id')->nullable()->index();
            $table->text('curriculo')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('correo', 120)->nullable()->index();
            $table->string('imagen_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sacerdotes');
    }
};
