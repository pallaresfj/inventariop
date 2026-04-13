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
        Schema::create('parishes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deanery_id')->index();
            $table->string('name', 80);
            $table->string('legacy_login')->nullable()->unique();
            $table->string('email', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('address', 120)->nullable();
            $table->string('phone', 30)->nullable()->index();
            $table->string('web', 120)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->unique(['deanery_id', 'name'], 'parishes_deanery_name_unique');

            $table->foreign('deanery_id')
                ->references('id')
                ->on('deaneries')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parishes');
    }
};
