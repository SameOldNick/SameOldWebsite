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
        Schema::dropIfExists('commenters');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commenters', function (Blueprint $table) {
            //
        });
    }
};
