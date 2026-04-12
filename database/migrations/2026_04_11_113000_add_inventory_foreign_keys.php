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
        Schema::table('parroquias', function (Blueprint $table): void {
            $table->foreign('arciprestazgo_id')
                ->references('id')
                ->on('arciprestazgos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('comunidades', function (Blueprint $table): void {
            $table->foreign('parroquia_id')
                ->references('id')
                ->on('parroquias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('sacerdotes', function (Blueprint $table): void {
            $table->foreign('titulo_sacerdotal_id')
                ->references('id')
                ->on('titulos_sacerdotales')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('arciprestazgos', function (Blueprint $table): void {
            $table->foreign('arcipestre_id')
                ->references('id')
                ->on('sacerdotes')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('articulos', function (Blueprint $table): void {
            $table->foreign('parroquia_id')
                ->references('id')
                ->on('parroquias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('comunidad_id')
                ->references('id')
                ->on('comunidades')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('restauraciones', function (Blueprint $table): void {
            $table->foreign('articulo_id')
                ->references('id')
                ->on('articulos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('asignacion_parroquia_sacerdotes', function (Blueprint $table): void {
            $table->foreign('parroquia_id')
                ->references('id')
                ->on('parroquias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('sacerdote_id')
                ->references('id')
                ->on('sacerdotes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('cargo_parroquial_id')
                ->references('id')
                ->on('cargos_parroquiales')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('arciprestazgo_id')
                ->references('id')
                ->on('arciprestazgos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('parroquia_id')
                ->references('id')
                ->on('parroquias')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('comunidad_id')
                ->references('id')
                ->on('comunidades')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['arciprestazgo_id']);
            $table->dropForeign(['parroquia_id']);
            $table->dropForeign(['comunidad_id']);
        });

        Schema::table('asignacion_parroquia_sacerdotes', function (Blueprint $table): void {
            $table->dropForeign(['parroquia_id']);
            $table->dropForeign(['sacerdote_id']);
            $table->dropForeign(['cargo_parroquial_id']);
        });

        Schema::table('restauraciones', function (Blueprint $table): void {
            $table->dropForeign(['articulo_id']);
        });

        Schema::table('articulos', function (Blueprint $table): void {
            $table->dropForeign(['parroquia_id']);
            $table->dropForeign(['comunidad_id']);
        });

        Schema::table('arciprestazgos', function (Blueprint $table): void {
            $table->dropForeign(['arcipestre_id']);
        });

        Schema::table('sacerdotes', function (Blueprint $table): void {
            $table->dropForeign(['titulo_sacerdotal_id']);
        });

        Schema::table('comunidades', function (Blueprint $table): void {
            $table->dropForeign(['parroquia_id']);
        });

        Schema::table('parroquias', function (Blueprint $table): void {
            $table->dropForeign(['arciprestazgo_id']);
        });
    }
};
