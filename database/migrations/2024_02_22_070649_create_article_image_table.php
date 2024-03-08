<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained();
            $table->foreignUuid('image_uuid')->constrained(null, 'uuid');
        });

        // Transfers over existing images (if there are any)
        DB::table('article_image')->insertUsing([
            'article_id', 'image_uuid',
        ], DB::table('images')->select(
            'article_id', 'uuid'
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_image');
    }
};
