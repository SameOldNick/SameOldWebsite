<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->foreignUuid('parent_revision_uuid')->nullable()->after('article_id')->constrained('revisions', 'uuid')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->dropForeign(['parent_revision']);
            $table->dropColumn('parent_revision');
        });
    }
};
