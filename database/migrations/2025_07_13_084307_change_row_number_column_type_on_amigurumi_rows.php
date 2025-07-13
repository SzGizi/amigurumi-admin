<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('amigurumi_rows', function (Blueprint $table) {
            $table->string('row_number')->change();
        });
    }

    public function down()
    {
        Schema::table('amigurumi_rows', function (Blueprint $table) {
            $table->unsignedInteger('row_number')->change();
        });
    }
};
