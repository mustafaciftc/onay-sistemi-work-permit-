<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptimizeTables extends Migration
{
    public function up()
    {
        // users tablosunu güncelle
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable()->after('email');
            $table->foreignId('department_id')->nullable()->after('position')->constrained('company_departments');
            $table->string('role')->default('user')->after('department_id');
            $table->dropColumn('company_id'); // Gereksiz
        });

        // work_permit_forms tablosunu optimize et
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->foreignId('current_approver_id')->nullable()->constrained('users');
            $table->string('current_step')->default('unit_manager');
            $table->json('approval_sequence')->nullable();
            $table->json('approvals_history')->nullable();

            // Gereksiz sütunları kaldır
            $table->dropColumn(['area_manager_id', 'safety_specialist_id', 'employer_representative_id']);
        });

        // Eski tabloları sil
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('work_permit_approval_steps');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('company_department_users');
    }

    public function down()
    {
        // Rollback logic
    }
}
