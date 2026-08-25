<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
        $table->string('name');
        $table->string('type');
        $table->json('parameters');
        $table->string('result_path')->nullable();
        $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
        $table->timestamp('generated_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('reports');
}
};
