<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            // Açılış süreci alanları
            $table->string('worker_name')->nullable();
            $table->string('worker_position')->nullable();
            $table->text('tools_equipment')->nullable();
            $table->text('emergency_procedures')->nullable();

            // Alan amiri onayı
            $table->boolean('energy_cut_off')->default(false);
            $table->boolean('area_cleaned')->default(false);
            $table->boolean('no_conflict_with_other_works')->default(false);
            $table->text('area_manager_notes')->nullable();
            $table->timestamp('area_manager_approved_at')->nullable();

            // İSG onayı
            $table->boolean('gas_measurement_done')->default(false);
            $table->boolean('ppe_checked')->default(false);
            $table->boolean('additional_procedures_verified')->default(false);
            $table->text('safety_specialist_notes')->nullable();
            $table->timestamp('safety_specialist_approved_at')->nullable();

            // Kapanış süreci
            $table->boolean('work_completed')->default(false);
            $table->boolean('equipment_collected')->default(false);
            $table->boolean('emergency_equipment_closed')->default(false);
            $table->boolean('fire_risk_eliminated')->default(false);
            $table->boolean('cleaning_done')->default(false);
            $table->text('closing_photos')->nullable(); // JSON formatında fotoğraf URL'leri

            // Final onay
            $table->timestamp('employer_representative_approved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Onaycılar
            $table->foreignId('area_manager_id')->nullable()->constrained('users');
            $table->foreignId('safety_specialist_id')->nullable()->constrained('users');
            $table->foreignId('employer_representative_id')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropColumn([
                'worker_name', 'worker_position', 'tools_equipment', 'emergency_procedures',
                'energy_cut_off', 'area_cleaned', 'no_conflict_with_other_works', 'area_manager_notes',
                'area_manager_approved_at', 'gas_measurement_done', 'ppe_checked',
                'additional_procedures_verified', 'safety_specialist_notes', 'safety_specialist_approved_at',
                'work_completed', 'equipment_collected', 'emergency_equipment_closed',
                'fire_risk_eliminated', 'cleaning_done', 'closing_photos',
                'employer_representative_approved_at', 'closed_at',
                'area_manager_id', 'safety_specialist_id', 'employer_representative_id'
            ]);
        });
    }
};
