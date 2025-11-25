<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->string('final_pdf_path')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropColumn('final_pdf_path');
        });
    }
};
