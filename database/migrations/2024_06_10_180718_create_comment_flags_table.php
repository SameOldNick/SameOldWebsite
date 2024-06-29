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
        Schema::create('comment_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->string('reason'); // Why the comment was flagged
            $table->text('proposed')->nullable(); // Proposed changes to the comment
            $table->json('extra')->nullable(); // Any extra information about flag.
            $table->unsignedBigInteger('deleted_by')->nullable(); // Who approved the removal
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_flags');
    }
};
