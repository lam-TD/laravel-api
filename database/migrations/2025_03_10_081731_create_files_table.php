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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('path', 255);
            $table->string('type', 255);
            $table->bigInteger('size');
            $table->string('extension', 255);
            $table->integer('status')->default(1);
            $table->string('description', 255)->nullable();

            // encrypt: key, iv
            $table->string('encrypt_key', 255)->nullable();
            $table->string('encrypt_iv', 255)->nullable();

            $table->morphs('fileable');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
