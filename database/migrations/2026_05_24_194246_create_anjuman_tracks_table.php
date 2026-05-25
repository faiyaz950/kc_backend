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
        Schema::create('anjuman_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anjuman_id')->constrained('anjumans')->onDelete('cascade');
            $table->string('title');
            $table->string('audio_url');
            $table->string('image_url')->nullable();
            $table->string('duration')->nullable();
            $table->integer('play_count')->default(0);
            $table->string('occasion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anjuman_tracks');
    }
};
