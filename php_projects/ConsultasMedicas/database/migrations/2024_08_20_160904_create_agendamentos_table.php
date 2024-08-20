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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->string('turno');
            $table->string('nome_medico');
            $table->text('mes');
            $table->string('endereco_consultorio'); 
            $table->decimal('preco', 8, 2);
            $table->string('modalidade');
            $table->string('especialidade');
            $table->string('crm_medico');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
