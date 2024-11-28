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
        Schema::create('filesystem_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('disk_type');
            $table->morphs('configurable', 'fs_configurations_configurable_type_id_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filesystem_configurations');
    }
};
