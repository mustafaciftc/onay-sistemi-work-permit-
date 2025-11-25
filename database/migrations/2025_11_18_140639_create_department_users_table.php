<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    if (!Schema::hasTable('department_users')) {
        Schema::create('department_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('department_users');
    }
};
