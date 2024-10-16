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
        Schema::table('users', function (Blueprint $table) {
            $table->after('avatar', function (Blueprint $table) {
                $table->string('address1')->nullable();
                $table->string('address2')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('city');
            $table->dropColumn('postal_code');
        });
    }
};
