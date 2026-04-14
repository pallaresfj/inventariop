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
        if (! Schema::hasColumn('parishes', 'legacy_login')) {
            return;
        }

        Schema::table('parishes', function (Blueprint $table) {
            $table->dropColumn('legacy_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('parishes', 'legacy_login')) {
            return;
        }

        Schema::table('parishes', function (Blueprint $table) {
            $table->string('legacy_login')->nullable()->unique();
        });
    }
};
