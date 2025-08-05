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
        Schema::create('amigurumi_pattern_assembly_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amigurumi_pattern_id')->constrained()->onDelete('cascade');
            $table->text('text')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amigurumi_pattern_assembly_steps');
    }
};
