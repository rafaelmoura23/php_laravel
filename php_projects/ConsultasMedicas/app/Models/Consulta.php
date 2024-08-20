<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_medico',
        'id_agendamento',
        'id_usuario',
        'data',
        'status',
        'observacoes'
    ];
}
