<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('work_permit_forms', 'permit_number')) {
                $table->integer('permit_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('work_permit_forms', 'permit_code')) {
                $table->string('permit_code')->unique()->nullable()->after('permit_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_permit_forms', function (Blueprint $table) {
            $table->dropColumn(['permit_number', 'permit_code']);
        });
    }
};
