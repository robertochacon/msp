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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->integer('total_completados')->default(0)->nullable();
            $table->integer('total_fallidos')->default(0)->nullable();
            $table->string('descripcion')->nullable();;
            $table->enum('tipo', ['Cliente', 'Credito', 'Movimiento'])->default('Cliente');
            $table->boolean('estado');
            $table->json('codes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
