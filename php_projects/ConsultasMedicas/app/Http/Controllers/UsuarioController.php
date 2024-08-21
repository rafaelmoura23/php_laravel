<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Agendamento;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    // Exibir o formulário de login
    public function showLoginForm()
    {
        return view('usuarios.login');
    }

    // Processar o login do usuário
    public function login(Request $request)
    {
        // Validações para o login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tenta autenticar com o guard 'usuario'
        if (Auth::guard('usuario')->attempt($credentials)) {
            $request->session()->regenerate(); // Regenera a sessão para evitar fixação de sessão
            return redirect()->intended('/dashboard');
        }

        // Se falhar, retorna com erro
        return back()->withErrors([
            'email' => 'As credenciais não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    // Exibir o formulário de registro
    public function showRegistroForm()
    {
        return view('usuarios.registro');
    }

    // Processar o registro de um novo usuário
    public function registro(Request $request)
    {
        // Validações para o registro
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios', // Atualize o nome da tabela conforme necessário
            'data_nascimento' => 'required|date',
            'telefone' => 'required|string|max:15',
            'endereco' => 'required|string|max:255',
            'plano_saude' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'tipo' => 'required|string|in:usuario,medico',
            'rg_usuario' => 'nullable|string|max:20',
            'crm_medico' => 'nullable|string|max:20',
            'especialidade' => 'nullable|string|max:255',
        ]);

        // Cria um novo usuário
        $usuario = Usuario::create([
            'nome' => $validatedData['nome'],
            'email' => $validatedData['email'],
            'data_nascimento' => $validatedData['data_nascimento'],
            'telefone' => $validatedData['telefone'],
            'endereco' => $validatedData['endereco'],
            'plano_saude' => $validatedData['plano_saude'],
            'password' => Hash::make($validatedData['password']),
            'tipo' => $validatedData['tipo'], // Armazena o tipo de usuário (usuario ou medico)
            'rg_usuario' => $validatedData['tipo'] === 'usuario' ? $validatedData['rg_usuario'] : null, // Inclui o RG apenas para usuários
            'crm_medico' => $validatedData['tipo'] === 'medico' ? $validatedData['crm_medico'] : null, // Inclui o CRM apenas para médicos
            'especialidade' => $validatedData['tipo'] === 'medico' ? $validatedData['especialidade'] : null, // Inclui a especialidade apenas para médicos
        ]);

        // Faz login automático do novo usuário
        Auth::login($usuario);

        // Redireciona para o dashboard ou a página inicial
        return redirect('/dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }


    // Realizar o logout do usuário
    public function logout(Request $request)
    {
        Auth::guard('usuario')->logout(); // Logout do guard 'usuario'
        $request->session()->regenerateToken(); // Regenera o token da sessão

        $request->session()->invalidate();
        $request->session()->regenerate(); // Invalida a sessão

        return redirect('/');
    }

    public function listarMedicos()
    {
        // Buscar todos os usuários que são médicos
        $medicos = Usuario::where('tipo', 'medico')->get();

        // Retornar a view com a lista de médicos
        return view('usuarios.medicos', ['medicos' => $medicos]);
    }

    // public function show($id)
    // {
    //     $medico = Usuario::with('agendamentos')->findOrFail($id);
    //     return view('usuarios.show', ['medico' => $medico]);
    // }



    // Exemplo de como seria no controller
    public function show($id)
{
    $medico = Usuario::findOrFail($id);
    
    // Recupera os agendamentos e agrupa por mês
    $agendamentos = $medico->agendamentos->groupBy(function($date) {
        return $date->mes;
    });

    $calendarios = [];
    
    foreach ($agendamentos as $mesAno => $agendamentosMes) {
        $calendarios[$mesAno] = [
            'mesAno' => \Carbon\Carbon::createFromFormat('Y-m', $mesAno),
            'agendamentos' => $agendamentosMes
        ];
    }
    
    // Adiciona lógica para gerar dias e horários
    foreach ($calendarios as &$calendario) {
        $diasMes = collect();
        $mesAno = $calendario['mesAno'];

        for ($day = 1; $day <= $mesAno->daysInMonth; $day++) {
            $diasMes->push($mesAno->copy()->day($day));
        }

        $calendario['dias'] = $diasMes;
        $calendario['horarios'] = [];

        foreach ($diasMes as $dia) {
            $turno = $calendario['agendamentos']->where('mes', $dia->format('Y-m'))->first()->turno ?? null;

            if ($turno) {
                $horarios = [];
                if ($turno == 'manhã') {
                    for ($hora = 8; $hora < 12; $hora += 0.5) {
                        $horarios[] = sprintf('%02d:%02d', floor($hora), ($hora - floor($hora)) * 60);
                    }
                } elseif ($turno == 'tarde') {
                    for ($hora = 14; $hora < 18; $hora += 0.5) {
                        $horarios[] = sprintf('%02d:%02d', floor($hora), ($hora - floor($hora)) * 60);
                    }
                }
                $calendario['horarios'][$dia->format('Y-m-d')] = $horarios;
            }
        }
    }

    return view('usuarios.show', [
        'medico' => $medico,
        'calendarios' => $calendarios
    ]);
}


}
