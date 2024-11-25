<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('one_time_passcode_secrets', function (Blueprint $table) {
            // Encrypted strings need to have the TEXT type
            $table->text('auth_secret')->change();
            $table->text('backup_secret')->change();
        });

        foreach (DB::table('one_time_passcode_secrets')->get() as $row) {
            DB::table('one_time_passcode_secrets')->where('id', $row->id)->update([
                // The encrypted cast doesn't serialize the data
                'auth_secret' => encrypt($row->auth_secret, false),
                'backup_secret' => encrypt($row->backup_secret, false),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (DB::table('one_time_passcode_secrets')->get() as $row) {
            DB::table('one_time_passcode_secrets')->where('id', $row->id)->update([
                'auth_secret' => decrypt($row->auth_secret, false),
                'backup_secret' => decrypt($row->backup_secret, false),
            ]);
        }

        Schema::table('one_time_passcode_secrets', function (Blueprint $table) {
            $table->string('auth_secret')->change();
            $table->string('backup_secret')->change();
        });
    }
};
