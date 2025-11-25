<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_departments', function (Blueprint $table) {
            // Onaycı ID'lerini ekle
            $table->foreignId('unit_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('area_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('safety_specialist_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('employer_representative_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('company_departments', function (Blueprint $table) {
            $table->dropForeign(['unit_manager_id']);
            $table->dropForeign(['area_manager_id']);
            $table->dropForeign(['safety_specialist_id']);
            $table->dropForeign(['employer_representative_id']);

            $table->dropColumn([
                'unit_manager_id',
                'area_manager_id',
                'safety_specialist_id',
                'employer_representative_id'
            ]);
        });
    }
};
