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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parish_id')->index();
            $table->string('name', 80);
            $table->string('legacy_login')->nullable()->unique();
            $table->string('email', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('address', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->unique(['parish_id', 'name'], 'communities_parish_name_unique');

            $table->foreign('parish_id')
                ->references('id')
                ->on('parishes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
