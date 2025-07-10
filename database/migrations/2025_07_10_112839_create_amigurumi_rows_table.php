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
        Schema::create('amigurumi_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amigurumi_section_id')->constrained('amigurumi_sections')->onDelete('cascade');
            $table->integer('row_number');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amigurumi_rows');
    }
};
