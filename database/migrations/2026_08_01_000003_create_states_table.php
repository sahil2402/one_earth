<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('country_id');
                $table->string('state_type');
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('image_path')->nullable();
                $table->boolean('our_operation')->default(false);
                $table->boolean('is_capital')->default(false);
                $table->string('lat_log_name')->nullable();
                $table->string('address')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
