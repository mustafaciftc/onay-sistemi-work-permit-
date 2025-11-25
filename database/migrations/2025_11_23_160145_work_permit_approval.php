<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_permit_approvals', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->foreignId('work_permit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Onaylayan kişi

            // Onay Tipi
            $table->enum('approval_type', [
                // Açılış onayları
                'area_manager',                 // Alan Amiri (açılış)
                'safety_expert',                // İSG Uzmanı (izni açma)

                // Kapatma onayları
                'area_manager_close',           // Alan Amiri (kapatma)
                'safety_expert_close',          // İSG Uzmanı (kapatma)
                'employer_representative',      // İşveren Vekili (final)
            ]);

            // Onay Durumu
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Onay Detayları
            $table->text('notes')->nullable(); // Onay notu / Red nedeni
            $table->json('checklist')->nullable(); // Kontrol listesi (İSG için)
            $table->timestamp('approved_at')->nullable(); // Onay tarihi
            $table->timestamp('rejected_at')->nullable(); // Red tarihi

            $table->timestamps();

            // İndeksler
            $table->index(['work_permit_id', 'approval_type']);
            $table->index(['user_id', 'status']);

            // Unique: Aynı kişi, aynı izin için, aynı tip onayı bir kere verebilir
            $table->unique(['work_permit_id', 'user_id', 'approval_type'], 'wp_approval_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_approvals');
    }
};
