<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 📌 AÇILIŞ SÜRECİ (4 aşama):
     * 1. pending_unit_approval → Birim Amiri onayı
     * 2. pending_area_approval → Alan Amiri onayı
     * 3. pending_safety_approval → İSG Uzmanı onayı
     * 4. pending_employer_approval → İşveren Vekili onayı (AÇILIŞ İÇİN YENİ)
     * ↓
     * approved → Çalışma başlayabilir
     *
     * 📌 KAPATMA SÜRECİ (4 aşama):
     * 1. closing_requested → Birim Amiri kapatma talebi
     * 2. pending_area_closing → Alan Amiri kapatma onayı
     * 3. pending_safety_closing → İSG Uzmanı kapatma onayı
     * 4. pending_employer_closing → İşveren Vekili final onayı (KAPATMA İÇİN YENİ)
     * ↓
     * completed → Tamamlandı
     */
    public function up()
    {
        // work_permit_forms tablosunun status enum'unu güncelle
        Schema::table('work_permit_forms', function (Blueprint $table) {
            // Eski enum'u kaldırıp yenisini ekle
            // MySQL'de bu işlemi yapabilmek için raw SQL kullanıyoruz
        });

        // Veya alternatif olarak, tüm tabloyu yeniden oluştur
        DB::statement("ALTER TABLE work_permit_forms CHANGE COLUMN status status VARCHAR(50) NOT NULL DEFAULT 'pending_unit_approval'");
    }

    public function down()
    {
        // Geri alma işlemi
        DB::statement("ALTER TABLE work_permit_forms CHANGE COLUMN status status VARCHAR(50) NOT NULL DEFAULT 'pending_unit_approval'");
    }
};
