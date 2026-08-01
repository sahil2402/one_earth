<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('country_id');
                $table->unsignedBigInteger('state_id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('time_to_visit')->nullable();
                $table->string('currency')->nullable();
                $table->string('language')->nullable();
                $table->text('introduction')->nullable();
                $table->string('lat_log_name')->nullable();
                $table->string('address')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->text('description')->nullable();
                $table->string('seo_title')->nullable();
                $table->string('meta_keyword')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('thumb_image')->nullable();
                $table->boolean('our_operation')->default(false);
                $table->boolean('is_capital')->default(false);
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
                $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
