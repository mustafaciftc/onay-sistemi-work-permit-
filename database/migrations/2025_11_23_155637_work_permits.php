<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_code')->unique(); // İzin numarası: WP-2025-0001

            // Temel Bilgiler
            $table->string('title'); // İş başlığı
            $table->text('description'); // İş açıklaması
            $table->string('location'); // Çalışma yeri
            $table->enum('work_type', ['sıcak', 'elektrik', 'yuk_kaldirma', 'kazı']); // İş türü

            // Tarih ve Saat
            $table->dateTime('start_date'); // Başlangıç
            $table->dateTime('end_date'); // Bitiş

            // Durum Yönetimi
            $table->enum('status', [
                // Açılış aşamaları
                'pending_area_approval',        // Alan Amiri onayı bekliyor
                'pending_safety_approval',      // İSG Uzmanı onayı bekliyor (açılış)
                'approved',                     // İş devam ediyor

                // Kapatma aşamaları
                'work_completed',               // İş tamamlandı
                'pending_area_close',           // Alan Amiri kapatma onayı bekliyor
                'pending_safety_close',         // İSG Uzmanı kapatma onayı bekliyor
                'pending_employer_close',       // İşveren Vekili final onayı bekliyor
                'closed',                       // Kapatıldı

                // Diğer
                'rejected'                      // Reddedildi
            ])->default('pending_area_approval');

            // İlişkiler
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Şirket
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Oluşturan (Birim Amiri)

            // Risk ve Güvenlik
            $table->json('risks')->nullable(); // Riskler
            $table->json('control_measures')->nullable(); // Kontrol tedbirleri
            $table->json('required_ppe')->nullable(); // Gerekli KKD'ler

            // Kapatma Bilgileri
            $table->text('closing_notes')->nullable(); // Kapatma notları
            $table->timestamp('closed_at')->nullable(); // Kapatılma tarihi
            $table->string('pdf_path')->nullable(); // PDF dosya yolu

            $table->timestamps();
            $table->softDeletes();

            // İndeksler
            $table->index('status');
            $table->index('company_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permits');
    }
};
