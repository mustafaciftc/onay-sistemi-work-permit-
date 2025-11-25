<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // FormTemplate ile ilişkili form alanları
        if (!Schema::hasTable('form_fields')) {
            Schema::create('form_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('form_template_id')->constrained('form_templates')->onDelete('cascade');
                $table->string('name'); // field_name (örn: work_type, location)
                $table->string('label'); // Görünen ad (İş Türü, Çalışma Yeri)
                $table->enum('type', ['text', 'textarea', 'select', 'multiselect', 'checkbox', 'date', 'file', 'number', 'email'])->default('text');
                $table->json('options')->nullable(); // select/multiselect için seçenekler
                $table->text('description')->nullable(); // Yardım metni
                $table->boolean('required')->default(false);
                $table->integer('sort_order')->default(0);
                $table->json('validation_rules')->nullable(); // min, max, pattern vb
                $table->json('conditional_display')->nullable(); // Koşullu gösterim
                $table->timestamps();
            });
        }

        // Onay hiyerarşisini takip et
        if (!Schema::hasTable('work_permit_approval_steps')) {
            Schema::create('work_permit_approval_steps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('work_permit_form_id')->constrained('work_permit_forms')->onDelete('cascade');
                $table->enum('phase', ['opening', 'closing']); // Açılış veya Kapanış
                $table->enum('step', ['unit_manager', 'area_manager', 'safety_specialist', 'employer_representative']); // Rol
                $table->integer('order'); // Sıra (1, 2, 3, 4)
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('comments')->nullable();
                $table->json('checklist')->nullable(); // Kontrol listesi
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('work_permit_approval_steps');
        Schema::dropIfExists('form_fields');
    }
};
