<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('amigurumi_rows', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('row_number');
        });
    }

    public function down(): void {
        Schema::table('amigurumi_rows', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
