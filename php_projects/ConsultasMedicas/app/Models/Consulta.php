<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'horarios',
        'duracao',
        'tipo_consulta',
        'valor',
        'disponibilidade',
        'nome_medico',
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
