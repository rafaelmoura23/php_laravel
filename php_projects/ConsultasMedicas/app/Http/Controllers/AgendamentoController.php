<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    // Exibe uma lista de agendamentos
    public function index()
    {
        $medico = Auth::user()->crm_medico;
        $agendamentos = Agendamento::where('crm_medico', $medico)->get();
        return view('agendamentos.index', compact('agendamentos'));
    }

    // Mostra o formulário para criar um novo agendamento
    public function create()
    {
        return view('agendamentos.create');
    }

    // Salva um novo agendamento no banco de dados
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'turno' => 'required|string|max:255',
            'nome_medico' => 'required|string|max:255',
            'mes' => 'required|string',
            'endereco_consultorio' => 'required|string|max:255',
            'preco' => 'required|numeric',
            'modalidade' => 'required|string|max:255',
            'especialidade' => 'required|string|max:255',
            'crm_medico' => 'required|string|max:20',
        ]);

        Agendamento::create($validatedData);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento criado com sucesso.');
    }

    // Exibe um único agendamento
    public function show($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        return view('agendamentos.show', compact('agendamento'));
    }

    // Mostra o formulário para editar um agendamento existente
    public function edit($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        return view('agendamentos.edit', compact('agendamento'));
    }

    // Atualiza um agendamento no banco de dados
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'turno' => 'required|string|max:255',
            'nome_medico' => 'required|string|max:255',
            'mes' => 'required|string',
            'endereco_consultorio' => 'required|string|max:255',
            'preco' => 'required|numeric',
            'modalidade' => 'required|string|max:255',
            'especialidade' => 'required|string|max:255',
            'crm_medico' => 'required|string|max:20',
        ]);

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update($validatedData);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado com sucesso.');
    }

    // Exclui um agendamento do banco de dados
    public function destroy($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento excluído com sucesso.');
    }
}
