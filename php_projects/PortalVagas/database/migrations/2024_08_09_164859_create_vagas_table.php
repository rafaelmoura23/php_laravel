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
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->string('localizacao');
            $table->decimal('salario', 10, 2);
            $table->string('empresa');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }

};
