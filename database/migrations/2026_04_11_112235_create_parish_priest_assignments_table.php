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
        Schema::create('parish_priest_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parish_id')->index();
            $table->unsignedBigInteger('priest_id')->index();
            $table->unsignedBigInteger('parish_role_id')->nullable()->index();
            $table->boolean('is_current')->default(true)->index();
            $table->timestamps();

            $table->unique(
                ['parish_id', 'priest_id', 'parish_role_id'],
                'parish_priest_assignment_unique'
            );

            $table->foreign('parish_id')
                ->references('id')
                ->on('parishes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('priest_id')
                ->references('id')
                ->on('priests')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('parish_role_id')
                ->references('id')
                ->on('parish_roles')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parish_priest_assignments');
    }
};
