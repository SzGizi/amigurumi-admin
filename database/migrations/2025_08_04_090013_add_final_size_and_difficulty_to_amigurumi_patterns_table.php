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
        Schema::table('amigurumi_patterns', function (Blueprint $table) {
            $table->string('final_size')->nullable();
            $table->string('difficulty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amigurumi_patterns', function (Blueprint $table) {
            $table->dropColumn(['final_size', 'difficulty']);
        });
    }
};
