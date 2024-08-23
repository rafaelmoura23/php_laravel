# Passo a Passo - Projeto Laravel: Consultas Médicas 🩺

## 1. Criação do Projeto

Crie um novo projeto Laravel:

```bash
composer create laravel/laravel ConsultasMedicas --prefer-dist
```

Criação do banco de dados:
```bash
Create database consultas_medicas
```

Edição do arquivo `.env:`
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=consultas_medicas
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Criação dos `models:`
```
php artisan make:model Usuario -m
php artisan make:model Consulta -m
php artisan make:model Agendamento -m
```

Editar os `migrations`:
- usuarios
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('data_nascimento'); // Alterado para date
            $table->string('telefone');
            $table->string('endereco');
            $table->string('plano_saude');
            $table->string('rg_usuario')->nullable()->unique(); // Usuário
            $table->string('crm_medico')->nullable()->unique(); // Médico
            $table->string('especialidade')->nullable(); // Médico
            $table->enum('tipo', ['usuario', 'medico'])->default('usuario');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
```

- agendamentos
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->string('turno');
            $table->string('nome_medico');
            $table->text('mes');
            $table->string('endereco_consultorio'); 
            $table->decimal('preco', 8, 2);
            $table->string('modalidade');
            $table->string('especialidade');
            $table->string('crm_medico');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};

```

- consultas
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id(); // Primary key

            // Se crm_medico é uma referência à tabela de médicos, altere 'usuarios' para 'medicos'
            $table->string('crm_medico'); // Cria a coluna crm_medico como string
            $table->foreign('crm_medico')->references('crm_medico')->on('usuarios')->onDelete('cascade');

            // // Se id_agendamento é uma referência à tabela de agendamentos
            // $table->foreignId('id_agendamento')->constrained('agendamentos')->onDelete('cascade');

            // Se rg_usuario é uma referência à tabela de usuários
            $table->string('rg_usuario'); // Cria a coluna crm_medico como string
            $table->foreign('rg_usuario')->references('rg_usuario')->on('usuarios')->onDelete('cascade');

            // Se você quer armazenar a data e a hora juntos, continue com dateTime
            $table->date('data'); // Usar date apenas para data
            $table->time('horario'); // Usar time para horário apenas

            $table->string('status');
            $table->text('observacoes')->nullable();
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
```

Aplicar as alterações no banco de dados:
- ```php artisan migrate``` => (realizar alterações/criações das tabelas no banco de dado)

Criação do Controller para Usuario:
- ```php artisan make:controller UsuarioController```
```
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
        'rg_usuario' => 'nullable|digits:9',
        'crm_medico' => 'nullable|string|max:10',
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

```

Criar o layouts.app
- php artisan make:view layouts.app

Criar Header e Footer:
- php artisan make:view parts.header
- php artisan make:view parts.footer


Criar View:
- php artisan make:view usuarios.registro
- php artisan make:view usuarios.login
- php artisan make:view usuarios.dashboard

Criar a Home:
- php artisan make:view home

Criar HomeController
- php artisan make:controller HomeController

Criar DashboardController
- php artisan make:controller DashboardController



Definir as rotas em `routes/web.php:`
```
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Middleware\AgendamentoMiddleware;
use App\Http\Controllers\ConsultaController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rota para exibir o formulário de login
Route::get('/login', [UsuarioController::class, 'showLoginForm'])->name('login');


// Rota para processar o login
Route::post('/login', [UsuarioController::class, 'login'])->name('usuarios.login');


// Rota para exibir o formulário de registro
Route::get('/registro', [UsuarioController::class, 'showRegistroForm'])->name('usuarios.registro');


// Rota para processar o registro
Route::post('/registro', [UsuarioController::class, 'registro'])->name('usuarios.registro');


// Rota para logout
Route::post('/logout', [UsuarioController::class, 'logout'])->name('usuarios.logout');

// Rota para o dashboard, protegida por autenticação
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Rota para agendamentos
Route::resource('/agendamentos', AgendamentoController::class)->middleware(AgendamentoMiddleware::class);

// Rota para lista dos médicos cadastrados
Route::get('/medicos', [UsuarioController::class, 'listarMedicos'])->middleware('auth')->name('medicos.index');

// Rota para ver os horários dos médicos
Route::get('usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');


Route::get('/consulta/create', [ConsultaController::class, 'create'])->middleware('auth')->name('consulta.create');


Route::post('/consulta/store', [ConsultaController::class, 'store'])->middleware('auth')->name('consulta.store');

// Rota para exibir o formulário de edição
Route::get('/consulta/{id}/edit', [ConsultaController::class, 'edit'])->name('consulta.edit');

// Rota para atualizar uma consulta existente
Route::put('/consulta/{id}', [ConsultaController::class, 'update'])->name('consulta.update');

Route::resource('consultas', ConsultaController::class)->middleware('auth');

```

