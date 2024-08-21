<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_medico',
        'rg_usuario',
        'data',
        'status',
        'horario',
        'observacoes'
    ];
}
