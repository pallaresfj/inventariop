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
        Schema::create('restorations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->index();
            $table->date('restored_at');
            $table->unsignedInteger('restoration_cost')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'restored_at'], 'restorations_item_restored_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restorations');
    }
};
