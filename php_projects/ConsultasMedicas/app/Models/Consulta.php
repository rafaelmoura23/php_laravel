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

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'crm_medico', 'crm_medico');
    }

    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'rg_usuario', 'rg_usuario');
    }
}
