<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'banned_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            });
        }

        DB::table('users')
            ->where('is_banned', true)
            ->whereNull('banned_at')
            ->update(['banned_at' => now()]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'banned_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('banned_at');
            });
        }
    }
};
