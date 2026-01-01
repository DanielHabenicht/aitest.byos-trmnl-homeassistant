<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('homeassistant_entities', function (Blueprint $table) {
            $table->id();
            $table->string('entity_id')->unique();
            $table->string('display_name')->nullable();
            $table->string('format')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('homeassistant_entities');
    }
};
