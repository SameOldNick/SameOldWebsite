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
        Schema::create('contact_message_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('contact_message_uuid')->constrained(null, 'uuid');
            $table->string('reason'); // Why the message was flagged
            $table->json('extra')->nullable(); // Any extra information about flag.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_message_flags');
    }
};
