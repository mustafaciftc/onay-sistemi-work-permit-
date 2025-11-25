<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_permit_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_permit_id')->constrained('work_permit_forms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['opening', 'closing']);
            $table->enum('step', [
                'unit_manager',
                'area_manager',
                'safety_specialist',
                'employer_representative'
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_permit_approvals');
    }
};
