<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_menu_permissions', function (Blueprint $table): void {
            $table->boolean('can_view')->default(false)->after('can_create');
        });
    }

    public function down(): void
    {
        Schema::table('role_menu_permissions', function (Blueprint $table): void {
            $table->dropColumn('can_view');
        });
    }
};
