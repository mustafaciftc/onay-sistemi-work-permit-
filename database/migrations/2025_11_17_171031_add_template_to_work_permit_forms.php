<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('work_permit_forms', 'form_template_id')) {
                $table->unsignedBigInteger('form_template_id')->nullable()->after('id');

                // Sadece form_templates tablosu varsa foreign key ekle
                if (Schema::hasTable('form_templates')) {
                    $table->foreign('form_template_id')
                          ->references('id')
                          ->on('form_templates')
                          ->onDelete('set null');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            // Foreign key'i drop et
            $table->dropForeign(['form_template_id']);
            $table->dropColumn('form_template_id');
        });
    }
};
