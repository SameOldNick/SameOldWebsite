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
        /**
         * Rebuilds images table since SQLite doesn't support removing columns
         * Adding rows will cause integrity failures.
         */
        if (app('db.connection') instanceof SQLiteConnection) {
            Schema::rename('images', 'old_images');

            Schema::create('images', function (Blueprint $table) {
                $table->uuid()->primary();
                $table->string('description')->nullable();
            });

            DB::table('images')->insertUsing([
                'uuid', 'description',
            ], DB::table('old_images')->select(
                'uuid', 'description'
            ));

            Schema::drop('old_images');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            //
        });
    }
};
