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
        Schema::table('pending_messages', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('updated_at');
            $table->timestamp('expires_at')->nullable()->default(null)->change();

            $table->rename('contact_messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->rename('pending_messages');
        });
    }
};
