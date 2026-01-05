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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Core content
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description'); // Short summary for cards
            $table->longText('content'); // Detailed Editor.js JSON
            
            // Project Metadata
            $table->string('url_live')->nullable();
            $table->string('url_repo')->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            
            // Workflow & Visibility
            $table->string('status')->default('completed'); // e.g., in_progress, completed, archived
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0); // For manual sorting
            
            // Ownership
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexing for performance
            $table->index(['is_featured', 'sort_order']);
        });

        // Pivot table for Tech Stack (reuse Tags)
        Schema::create('project_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->unique(['project_id', 'tag_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tags');
        Schema::dropIfExists('projects');
    }
};