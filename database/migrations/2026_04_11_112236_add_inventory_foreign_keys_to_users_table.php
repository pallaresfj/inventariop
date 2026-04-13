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
        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('deanery_id')
                ->references('id')
                ->on('deaneries')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('parish_id')
                ->references('id')
                ->on('parishes')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
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
            $table->dropForeign(['deanery_id']);
            $table->dropForeign(['parish_id']);
            $table->dropForeign(['community_id']);
        });
    }
};
