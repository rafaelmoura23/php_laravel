<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'turno',
        'nome_medico',
        'mes',
        'endereco_consultorio',
        'preco',
        'modalidade',
        'especialidade',
        'crm_medico'
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    public function usuario()
{
    return $this->belongsTo(Usuario::class);
}

}
