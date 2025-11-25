<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

    Schema::create('shareable_links', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('work_permit_id');
        $table->string('token');
        $table->string('password')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->integer('max_views')->nullable();
        $table->integer('view_count')->default(0);
        $table->boolean('is_active')->default(true);
        $table->json('permissions')->nullable();
        $table->timestamps();

        $table->foreign('work_permit_id')
              ->references('id')
              ->on('work_permit_forms')
              ->onDelete('cascade');

        $table->unique('token');
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('shareable_links');
    }
};
