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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parish_id')->index();
            $table->unsignedBigInteger('community_id')->index();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('condition', ['B', 'M', 'R'])->default('B')->index();
            $table->unsignedInteger('price')->default(0);
            $table->date('acquired_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['parish_id', 'community_id'], 'items_parish_community_index');

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
