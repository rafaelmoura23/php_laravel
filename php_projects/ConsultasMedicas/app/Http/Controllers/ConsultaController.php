<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Agendamento;

class ConsultaController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->input('data');
        $horario = $request->input('horario');
        $crm = $request->input('crm');
        
        return view('consultas.create', [
            'data' => $data,
            'horario' => $horario,
            'crm' => $crm
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'horario' => 'required|string',
            'crm' => 'required|string',
            'rg_usuario' => 'required|string',
            'observacoes' => 'nullable|string'
        ]);

        // Cria uma nova consulta
        $consulta = new Consulta();
        $consulta->data = $request->input('data');
        $consulta->horario = $request->input('horario');
        $consulta->crm_medico = $request->input('crm');
        $consulta->rg_usuario = $request->input('rg_usuario');
        $consulta->observacoes = $request->input('observacoes');
        $consulta->status = 'agendada'; // Defina o status conforme necessário
        $consulta->save();

        // Atualiza o status do horário na tabela agendamentos

        return redirect()->route('home')->with('message', 'Consulta agendada com sucesso!');
    }
}
