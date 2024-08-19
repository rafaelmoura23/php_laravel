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
            $table->foreignId('rg_usuario')->constrained('usuarios')->
            onDelete('cascade');  // Relaciona o agendamento ao usuário
            $table->foreignId('crm_medico')->constrained('usuarios')->
            onDelete('cascade');  // Relaciona o agendamento ao medico
            $table->foreignId('id_consulta')->constrained('consultas')->
            onDelete('cascade'); // Relaciona o agendamento a Consultas
            $table->string('status')->default('Á realizar');
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
