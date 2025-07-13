<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('amigurumi_sections', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->nullable(false)->change();
        });
    }

    public function down(): void {
        Schema::table('amigurumi_sections', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable()->default(null)->change();
        });
    }
};
