<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Sütunu unsignedBigInteger olarak oluşturuyoruz
            $table->unsignedBigInteger('company_id')->nullable()->after('role');

            // Eğer bir foreign key (yabancı anahtar) kısıtlaması eklemek isterseniz:
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop yapmadan önce foreign key'i kaldırmanız gerekebilir (Eğer eklediyseniz)
            // $table->dropForeign(['company_id']);

            $table->dropColumn('company_id');
        });
    }
};
