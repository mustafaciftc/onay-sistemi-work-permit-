<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            // Eksik kolonları ekle
            if (!Schema::hasColumn('work_permit_forms', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('company_departments')->onDelete('cascade');
            }

            if (!Schema::hasColumn('work_permit_forms', 'position_id')) {
                $table->foreignId('position_id')->nullable()->constrained('department_positions')->onDelete('cascade');
            }

            // Diğer eksik kolonları kontrol et
            $columns = ['company_id', 'created_by', 'worker_position', 'permit_number', 'permit_code'];
            foreach ($columns as $column) {
                if (!Schema::hasColumn('work_permit_forms', $column)) {
                    // Bu kolonları da ekle gerekirse
                }
            }
        });
    }

    public function down()
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');

            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
