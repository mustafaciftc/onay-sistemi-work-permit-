<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tablo zaten varsa skip et
        if (!Schema::hasTable('company_departments')) {
            Schema::create('company_departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->json('approval_workflow')->nullable(); // Departmana özel onay akışı
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Mevcut tablolara department_id ekle (varsa)
        if (Schema::hasTable('form_templates') && !Schema::hasColumn('form_templates', 'department_id')) {
            Schema::table('form_templates', function (Blueprint $table) {
                $table->foreignId('department_id')->nullable()->constrained('company_departments')->onDelete('set null');
            });
        }

        if (Schema::hasTable('work_permit_forms') && !Schema::hasColumn('work_permit_forms', 'department_id')) {
            Schema::table('work_permit_forms', function (Blueprint $table) {
                $table->foreignId('department_id')->nullable()->constrained('company_departments')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        Schema::table('form_templates', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        Schema::dropIfExists('company_departments');
    }
};
