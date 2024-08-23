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
        // Validação dos dados
        $request->validate([
            'data' => 'required|date',
            'horario' => 'required|string',
            'crm' => 'required|string',
            'rg_usuario' => 'required|string',
            'observacoes' => 'nullable|string'
        ]);

        // Verifica se já existe uma consulta com a mesma data, horário e CRM do médico
        $consultaExistente = Consulta::where('data', $request->input('data'))
            ->where('horario', $request->input('horario'))
            ->where('crm_medico', $request->input('crm'))
            ->first();

        // Se já existir, retorna uma mensagem de erro
        if ($consultaExistente) {
            return redirect()->back()->with('error', 'Já existe uma consulta agendada para essa data e horário.');
        }

        // Cria uma nova consulta
        $consulta = new Consulta();
        $consulta->data = $request->input('data');
        $consulta->horario = $request->input('horario');
        $consulta->crm_medico = $request->input('crm');
        $consulta->rg_usuario = $request->input('rg_usuario');
        $consulta->observacoes = $request->input('observacoes');
        $consulta->status = 'agendada';
        $consulta->save();

        return redirect()->route('dashboard')->with('message', 'Consulta agendada com sucesso!');
    }


    public function edit($id)
    {
        $consulta = Consulta::findOrFail($id);

        return view('consultas.edit', compact('consulta'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'data' => 'required|date',
            'horario' => 'required|string',
            'crm' => 'required|string',
            'rg_usuario' => 'required|string',
            'observacoes' => 'nullable|string'
        ]);

        $consulta = Consulta::findOrFail($id);
        $consulta->data = $request->input('data');
        $consulta->horario = $request->input('horario');
        $consulta->crm_medico = $request->input('crm');
        $consulta->rg_usuario = $request->input('rg_usuario');
        $consulta->observacoes = $request->input('observacoes');
        $consulta->status = $consulta->status;
        $consulta->save();

        return redirect()->route('dashboard')->with('message', 'Consulta atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return redirect()->back()->with('message', 'Consulta excluída com sucesso!');
    }
}