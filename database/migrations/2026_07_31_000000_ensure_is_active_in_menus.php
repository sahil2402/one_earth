<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the column only if it doesn't already exist (safe to run on existing DBs)
        if (! Schema::hasColumn('menus', 'is_active')) {
            Schema::table('menus', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->after('parent_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menus', 'is_active')) {
            Schema::table('menus', function (Blueprint $table): void {
                $table->dropColumn('is_active');
            });
        }
    }
};
