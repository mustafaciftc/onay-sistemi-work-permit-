<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL için enum güncelleme
        DB::statement("ALTER TABLE work_permit_forms MODIFY COLUMN status ENUM(
            'pending_unit_approval',
            'pending_area_approval',
            'pending_safety_approval',
            'pending_employer_approval',
            'approved',
            'closing_requested',
            'pending_area_closing',
            'pending_safety_closing',
            'pending_employer_closing',
            'completed',
            'rejected'
        ) DEFAULT 'pending_unit_approval'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE work_permit_forms MODIFY COLUMN status ENUM(
            'pending_unit_approval',
            'pending_area_approval',
            'pending_safety_approval',
            'approved',
            'closing_requested',
            'pending_area_closing',
            'pending_safety_closing',
            'pending_final_closing',
            'completed',
            'rejected'
        ) DEFAULT 'pending_unit_approval'");
    }
};
