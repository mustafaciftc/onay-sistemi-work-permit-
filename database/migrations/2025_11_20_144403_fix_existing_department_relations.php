<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sadece company_departments tablosunu oluştur, foreign key'leri ekleme
        if (!Schema::hasTable('company_departments')) {
            Schema::create('company_departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->json('approval_workflow')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Foreign key'leri sadece eğer yoksa ekle
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
        // Sadece company_departments tablosunu sil
        Schema::dropIfExists('company_departments');
    }
};
