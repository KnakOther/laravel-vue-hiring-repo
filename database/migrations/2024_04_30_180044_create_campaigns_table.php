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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->datetime('send_date');
            $table->string('email_name');
            $table->string('subject')->default('');
            $table->string('from_name')->default('');;
            $table->string('from_email')->default('');;
            $table->string('reply_email')->default('');;
            $table->string('knak_version')->default('1');;
            $table->string('knak_email_id');
            $table->longText('html');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
