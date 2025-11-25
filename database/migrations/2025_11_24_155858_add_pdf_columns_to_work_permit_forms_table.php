<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            // Önce pdf_path sütununu ekle (eğer yoksa)
            if (!Schema::hasColumn('work_permit_forms', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('emergency_procedures');
            }

            // Sonra final_pdf_path sütununu ekle
            if (!Schema::hasColumn('work_permit_forms', 'final_pdf_path')) {
                $table->string('final_pdf_path')->nullable()->after('pdf_path');
            }
        });
    }

    public function down()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'final_pdf_path']);
        });
    }
};
