<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_permit_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('work_type', ['sıcak', 'elektrik', 'yuk_kaldirma', 'kazı', 'diğer']);
            $table->text('work_description');
            $table->string('location');
            $table->json('risks');
            $table->json('control_measures');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', [
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
            ])->default('pending_unit_approval');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_permit_forms');
    }
};
