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
        Schema::create('campaign_recipient', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id')->unsigned()->index();
            $table->bigInteger('recipient_id')->unsigned()->index();
            $table->string('message_id')->nullable();
            $table->longText('html');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('complaint_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->string('failure_reason')->nullable();


            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('recipients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_recipient');
    }
};
