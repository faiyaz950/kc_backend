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
        Schema::table('users', function (Blueprint $table) {
            $table->json('favorites')->nullable()->after('remember_token');
            $table->json('recently_played')->nullable()->after('favorites');
            $table->boolean('is_admin')->default(false)->after('recently_played');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['favorites', 'recently_played', 'is_admin']);
        });
    }
};
