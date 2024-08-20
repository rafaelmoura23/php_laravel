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
            $table->foreignId('crm_medico')->constrained('usuarios')->onDelete('cascade'); // Assuming 'medicos' table exists
            // $table->foreignId('id_agendamento')->constrained('agendamentos')->onDelete('cascade'); // Assuming 'agendamentos' table exists
            $table->foreignId('rg_usuario')->constrained('usuarios')->onDelete('cascade'); // Assuming 'usuarios' table exists
            $table->dateTime('data'); 
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
