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
        Schema::create('filesystem_configuration_ftp', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(21);
            $table->string('username');
            $table->text('password');
            $table->string('root')->nullable();
            $table->json('extra')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filesystem_configuration_ftp');
    }
};
