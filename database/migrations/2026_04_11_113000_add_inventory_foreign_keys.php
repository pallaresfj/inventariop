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
        Schema::table('parishes', function (Blueprint $table): void {
            $table->foreign('deanery_id')
                ->references('id')
                ->on('deaneries')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('communities', function (Blueprint $table): void {
            $table->foreign('parish_id')
                ->references('id')
                ->on('parishes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('priests', function (Blueprint $table): void {
            $table->foreign('priest_title_id')
                ->references('id')
                ->on('priest_titles')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('deaneries', function (Blueprint $table): void {
            $table->foreign('archpriest_id')
                ->references('id')
                ->on('priests')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->foreign('parish_id')
                ->references('id')
                ->on('parishes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('restorations', function (Blueprint $table): void {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('parish_priest_assignments', function (Blueprint $table): void {
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

        Schema::table('parish_priest_assignments', function (Blueprint $table): void {
            $table->dropForeign(['parish_id']);
            $table->dropForeign(['priest_id']);
            $table->dropForeign(['parish_role_id']);
        });

        Schema::table('restorations', function (Blueprint $table): void {
            $table->dropForeign(['item_id']);
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->dropForeign(['parish_id']);
            $table->dropForeign(['community_id']);
        });

        Schema::table('deaneries', function (Blueprint $table): void {
            $table->dropForeign(['archpriest_id']);
        });

        Schema::table('priests', function (Blueprint $table): void {
            $table->dropForeign(['priest_title_id']);
        });

        Schema::table('communities', function (Blueprint $table): void {
            $table->dropForeign(['parish_id']);
        });

        Schema::table('parishes', function (Blueprint $table): void {
            $table->dropForeign(['deanery_id']);
        });
    }
};
