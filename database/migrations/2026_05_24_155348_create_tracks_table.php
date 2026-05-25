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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['dua', 'noha', 'manqabat', 'naat']);
            $table->foreignId('reciter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reciter_name')->nullable();
            $table->string('language')->nullable();
            $table->string('occasion')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('play_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->text('lyrics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
