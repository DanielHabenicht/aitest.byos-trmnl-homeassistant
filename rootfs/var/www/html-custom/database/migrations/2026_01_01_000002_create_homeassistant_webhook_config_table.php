<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('homeassistant_webhook_config', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_url')->nullable();
            $table->string('plugin_uuid')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('homeassistant_webhook_config');
    }
};
