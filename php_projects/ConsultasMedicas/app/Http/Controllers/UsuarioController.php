<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Agendamento;
use Carbon\Carbon;
use App\Models\Consulta;


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
        'email' => 'required|email|max:255|unique:usuarios',
        'data_nascimento' => 'required|date',
        'telefone' => 'required|digits:11',
        'endereco' => 'required|string|max:255',
        'plano_saude' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'tipo' => 'required|string|in:usuario,medico',
        'rg_usuario' => 'nullable|string',
        'crm_medico' => 'nullable|string',
        'especialidade' => 'nullable|string|max:255',
    ]);

    try {
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
        return redirect('/dashboard')->with('success', 'Cadastro realizado com sucesso!');

    } catch (\Exception $e) {

        return redirect()->back()->withErrors(['erro' => 'Ocorreu um erro ao tentar realizar o cadastro. Por favor, tente novamente.'])->withInput();
    }
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

    public function listarMedicos(Request $request)
    {
        // Inicia a consulta para buscar médicos
        $query = Usuario::where('tipo', 'medico');
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->input('nome') . '%');
        }
        if ($request->filled('especialidade')) {
            $query->where('especialidade', 'like', '%' . $request->input('especialidade') . '%');
        }
        if ($request->filled('plano_saude')) {
            $query->where('plano_saude', 'like', '%' . $request->input('plano_saude') . '%');
        }
        $medicos = $query->get();
        $especialidades = Usuario::where('tipo', 'medico')
                                ->pluck('especialidade')
                                ->unique()
                                ->sort()
                                ->values();
    
        // Retorna a view com a lista de médicos e especialidades
        return view('usuarios.medicos', [
            'medicos' => $medicos,
            'especialidades' => $especialidades
        ]);
    }

    public function show($id)
    {
        $medico = Usuario::findOrFail($id);

        // Recupera os agendamentos e agrupa por mês
        $agendamentos = $medico->agendamentos->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->data)->format('Y-m');
        });

        $calendarios = [];

        foreach ($agendamentos as $mesAno => $agendamentosMes) {
            $calendarios[$mesAno] = [
                'mesAno' => \Carbon\Carbon::createFromFormat('Y-m', $mesAno),
                'dias' => $this->getDiasDoMes($mesAno),
                'horarios' => $this->getHorariosPorDia($agendamentosMes, $mesAno, $medico->crm_medico)
            ];
        }

        return view('usuarios.show', [
            'medico' => $medico,
            'calendarios' => $calendarios
        ]);
    }


    private function getDiasDoMes($mesAno)
    {
        $mesAnoObj = \Carbon\Carbon::createFromFormat('Y-m', $mesAno);
        $dias = collect();

        for ($day = 1; $day <= $mesAnoObj->daysInMonth; $day++) {
            $dias->push($mesAnoObj->copy()->day($day));
        }

        return $dias;
    }

    private function getHorariosPorDia($agendamentos, $mesAno, $crm_medico)
    {
        $dias = $this->getDiasDoMes($mesAno);
        $horarios = [];

        foreach ($dias as $dia) {
            // Busca os horários ocupados para o dia específico, filtrados pelo CRM do médico
            $horariosOcupados = $this->getHorariosOcupados($dia, $crm_medico);
            $horariosDisponiveis = $this->generateHorarios('manhã');

            // Remove os horários ocupados dos horários disponíveis
            $horarios[$dia->format('Y-m-d')] = array_diff($horariosDisponiveis, $horariosOcupados);
        }

        return $horarios;
    }

    private function getHorariosOcupados($dia, $crm_medico)
    {
        $consultas = Consulta::whereDate('data', $dia->format('Y-m-d'))
                              ->where('crm_medico', $crm_medico)
                              ->get();
        return $consultas->pluck('horario')->toArray();
    }

    private function generateHorarios($turno)
    {
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
        return $horarios;
    }
}








