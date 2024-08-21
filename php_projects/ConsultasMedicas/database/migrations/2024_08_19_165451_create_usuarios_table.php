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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('data_nascimento'); // Alterado para date
            $table->string('telefone');
            $table->string('endereco');
            $table->string('plano_saude');
            $table->string('rg_usuario')->nullable()->unique(); // Usuário
            $table->string('crm_medico')->nullable()->unique(); // Médico
            $table->string('especialidade')->nullable(); // Médico
            $table->enum('tipo', ['usuario', 'medico'])->default('usuario');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
