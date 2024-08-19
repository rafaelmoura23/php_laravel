<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        // atributos comuns - ambos
        'nome',
        'email',
        'data_nascimento',
        'telefone',
        'endereco',
        'plano_saude',
        'tipo',
        'password',
        // usuarios
        'rg_usuario',
        // medico
        'crm_medico',
        'especialidade',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relação com AGENDAMENTOS criado
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // === USUARIO ===
    public function isUsuario()
    {
        return $this->tipo === 'usuario';
    }

    // === MEDICO ===
    public function isMedico()
    {
        return $this->tipo === 'medico';
    }
}
