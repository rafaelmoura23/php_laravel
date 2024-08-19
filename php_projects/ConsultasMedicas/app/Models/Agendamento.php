<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'rg_usuario',
        'crm_medico',
        'id_consulta',
        'status',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }


    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}
