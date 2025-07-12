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
        Schema::table('amigurumi_rows', function (Blueprint $table) {
            $table->integer('stitch_number')->nullable();
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('amigurumi_rows', function (Blueprint $table) {
                if (Schema::hasColumn('amigurumi_rows', 'stitch_number')) {
                $table->dropColumn('stitch_number');
            }
            if (Schema::hasColumn('amigurumi_rows', 'comment')) {
                $table->dropColumn('comment');
            }
        });
    }
};
