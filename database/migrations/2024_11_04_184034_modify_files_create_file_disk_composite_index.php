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
        Schema::table('files', function (Blueprint $table) {
            if (Schema::hasIndex('files', 'files_path_unique'))
                $table->dropUnique(['path']);

            $table->index(['path', 'disk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // Can't recreate index for path because there might be repeating path values

            $table->dropIndex(['path', 'disk']);
        });
    }
};
