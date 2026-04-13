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
        Schema::create('priests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->unsignedBigInteger('priest_title_id')->nullable()->index();
            $table->text('bio')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 120)->nullable()->index();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->foreign('priest_title_id')
                ->references('id')
                ->on('priest_titles')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priests');
    }
};
