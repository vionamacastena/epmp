<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->boolean('can_comment')->default(true);
            $table->boolean('can_approve')->default(false);
            $table->timestamps();
            
            $table->unique(['client_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_project');
    }
};
