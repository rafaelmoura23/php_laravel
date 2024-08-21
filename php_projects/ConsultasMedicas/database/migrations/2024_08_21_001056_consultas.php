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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id(); // Primary key

            // Se crm_medico é uma referência à tabela de médicos, altere 'usuarios' para 'medicos'
            $table->string('crm_medico'); // Cria a coluna crm_medico como string
            $table->foreign('crm_medico')->references('crm_medico')->on('usuarios')->onDelete('cascade');

            // // Se id_agendamento é uma referência à tabela de agendamentos
            // $table->foreignId('id_agendamento')->constrained('agendamentos')->onDelete('cascade');

            // Se rg_usuario é uma referência à tabela de usuários
            $table->string('rg_usuario'); // Cria a coluna crm_medico como string
            $table->foreign('rg_usuario')->references('rg_usuario')->on('usuarios')->onDelete('cascade');

            // Se você quer armazenar a data e a hora juntos, continue com dateTime
            $table->date('data'); // Usar date apenas para data
            $table->time('horario'); // Usar time para horário apenas

            $table->string('status');
            $table->text('observacoes')->nullable();
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
