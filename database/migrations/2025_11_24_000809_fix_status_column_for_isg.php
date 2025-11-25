<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixStatusColumnForIsg extends Migration
{
    public function up()
    {
        // Status kolonunu VARCHAR(100) yap
        DB::statement('ALTER TABLE work_permit_forms MODIFY status VARCHAR(100) NOT NULL');

        // Mevcut veriyi kontrol et ve gerekirse güncelle
        $forms = DB::table('work_permit_forms')->where('id', 1)->get();
        foreach ($forms as $form) {
            echo "Mevcut status: " . $form->status . "\n";
        }
    }

    public function down()
    {
        DB::statement('ALTER TABLE work_permit_forms MODIFY status VARCHAR(50) NOT NULL');
    }
}
