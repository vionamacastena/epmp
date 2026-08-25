<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanban_board_id')->constrained('kanban_boards')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#6b8c5c');
            $table->integer('position')->default(0);
            $table->integer('wip_limit')->nullable();
            $table->string('status_mapping')->nullable(); // Maps to task status
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_columns');
    }
};
