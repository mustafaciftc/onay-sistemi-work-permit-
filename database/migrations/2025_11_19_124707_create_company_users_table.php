<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('user'); // admin, user, manager
            $table->timestamps();

            // Aynı kullanıcı aynı firmada birden fazla kez olamaz
            $table->unique(['company_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_users');
    }
};
