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
            $table->text('ihris_token')->nullable()->after('profile_photo_path');
            $table->string('ihris_user_id')->nullable()->after('ihris_token');
            $table->boolean('is_api_user')->default(false)->after('ihris_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ihris_token', 'ihris_user_id', 'is_api_user']);
        });
    }
};

