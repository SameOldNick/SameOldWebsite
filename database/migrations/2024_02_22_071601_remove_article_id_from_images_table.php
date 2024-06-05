<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection() instanceof SQLiteConnection) {
            /**
             * Rebuilds images table since SQLite doesn't support removing columns
             * Adding rows will cause integrity failures.
             */
            Schema::create('new_images', function (Blueprint $table) {
                $table->uuid()->primary();
                $table->string('description')->nullable();
            });

            DB::table('new_images')->insertUsing([
                'uuid', 'description',
            ], DB::table('images')->select(
                'uuid', 'description'
            ));

            Schema::drop('images');
            Schema::rename('new_images', 'images');
        } else {
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeignSafe('article_images_article_id_foreign');

                $table->dropColumnSafe('article_id');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained();
        });
    }
};
