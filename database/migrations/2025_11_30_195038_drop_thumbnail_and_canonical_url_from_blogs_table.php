<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'canonical_url']);
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('featured_image');
            $table->string('canonical_url')->nullable()->after('meta_description');
        });
    }
};
