# Passo a Passo - Projeto Laravel: Consultas Médicas 🩺

Crie um novo projeto Laravel:

```php
composer create laravel/laravel ConsultasMedicas --prefer-dist
```

Criação do banco de dados:
```php
CREATE DATABASE consultas_medicas
```

Edição do arquivo `.env:`
``` php
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=consultas_medicas
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Criação dos `models:`
``` php
php artisan make:model Usuario -m
php artisan make:model Consulta -m
php artisan make:model Agendamento -m
```

Editar os `migrations`:
- usuarios
``` php
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
``` php
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
``` php
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
- ```php artisan migrate``` => (realizar alterações/criações das tabelas no banco de dados)

Criação do Controller para Usuario:
- ```php artisan make:controller UsuarioController```
``` php
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

Atualizar o ```config/auth.php``` para lidar com Usuario e não User:
``` php
<?php

return [


    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */


    'defaults' => [
        'guard' => 'usuario',
        'passwords' => 'users',
    ],


    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */


    'guards' => [
        'usuario' => [
            'driver' => 'session',
            'provider' => 'usuario',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */


    'providers' => [
        'usuario' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],


        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */


    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */


    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),


];
```


Criar o ```layouts.app:```
- ```php artisan make:view layouts.app```
``` php
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Consultas Médicas') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6/inputmask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>


    @include('parts.header')


    <div class="container">
        @yield('content')
    </div>

    @yield('scripts')
    @include('parts.footer')

</body>

</html>

```
Criar o **Header** e o **Footer**:
- `php artisan make:view parts.header`
``` php
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="bi bi-heart-pulse-fill"></i> CONSULT.IA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                @if (Auth::check())
                    <div class="d-flex align-items-center">
                        @if (Auth::user()->isMedico())
                            <a href="/agendamentos" class="btn btn-primary me-2">
                                <i class="bi bi-calendar-plus"></i> Agendamento
                            </a>
                            <a href="/dashboard" class="btn btn-secondary me-2">
                                <i class="bi bi-journal-check"></i> Consultas
                            </a>
                        @endif
                        <span class="me-3">Bem-vindo, <strong>{{ Auth::user()->nome }}</strong></span>
                        @if (Auth::user()->isUsuario())
                            <a href="/medicos" class="btn btn-primary me-2">
                                <i class="bi bi-person-circle"></i> Médicos
                            </a>
                            <a href="/dashboard" class="btn btn-secondary me-2">
                                <i class="bi bi-journal-medical"></i> Consultas
                            </a>
                        @endif
                        <form action="/logout" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="btn btn-outline-primary me-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="/registro" class="btn btn-warning">
                        <i class="bi bi-person-plus"></i> Sign-up
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
```

- `php artisan make:view parts.footer`
``` php
<div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
      <div class="col-md-4 d-flex align-items-center">
        <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
          <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
        </a>
        <span class="mb-3 mb-md-0 text-body-secondary">© 2024 Consultas Médicas, Inc</span>
      </div>
  
      <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
        <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#twitter"></use></svg></a></li>
        <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#instagram"></use></svg></a></li>
        <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24"><use xlink:href="#facebook"></use></svg></a></li>
      </ul>
    </footer>
  </div>
``` 

Criar a view Home:
- ```php artisan make:view home```
``` php
@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Cabeçalho -->
    <div class="text-center mb-5">
        <h1 class="display-4 font-weight-bold">Bem-vindo ao Sistema de Consultas</h1>
        <p class="lead">Organize e gerencie suas consultas de forma fácil e eficiente.</p>
        <h4>🏠</h4>
    </div>
    
    {{-- Cards --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Agendar Consultas</h5>
                    <p class="card-text">Agende suas consultas com médicos de forma rápida e prática.</p>
                    <a href="{{ route('consultas.create') }}" class="btn btn-primary">Agendar Agora</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Ver Consultas</h5>
                    <p class="card-text">Consulte o histórico e os detalhes das suas consultas agendadas.</p>
                    <a href="{{ route('consultas.index') }}" class="btn btn-primary">Ver Consultas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Gerenciar Médicos</h5>
                    <p class="card-text">Adicione e gerencie informações sobre médicos e suas especialidades.</p>
                    <a href="{{ route('medicos.index') }}" class="btn btn-primary">Gerenciar Médicos</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sobre --}}
    <div class="text-center mt-5">
        <h2>Sobre o Sistema</h2>
        <p class="lead">Este sistema foi desenvolvido para simplificar o processo de agendamento de consultas e gerenciar informações de médicos e pacientes de forma eficiente.</p>
        <p>Utilize o menu acima para acessar as funcionalidades principais e gerenciar suas consultas e médicos com facilidade.</p>
    </div>
</div>
@endsection
```

Criar o `controller` para Home:
``` php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
}
```


Criar **Views** para `Usuarios`:
- `php artisan make:view usuarios.registro`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <h1 class="text-center mb-5">Registrar-se</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Seleção Inicial do Tipo --}}
        <div id="tipo-selection" class="mb-5 d-flex justify-content-center gap-4">
            <div id="card-usuario" class="card tipo-card shadow-sm p-3 text-center" style="cursor: pointer;">
                <div class="card-body">
                    <i class="bi bi-person-circle display-4 text-primary"></i>
                    <h2 class="card-title mt-3">Usuário</h2>
                    <p>Registro para pacientes e usuários gerais</p>
                </div>
            </div>
            <div id="card-medico" class="card tipo-card shadow-sm p-3 text-center" style="cursor: pointer;">
                <div class="card-body">
                    <i class="bi bi-stethoscope display-4 text-success"></i>
                    <h2 class="card-title mt-3">Médico</h2>
                    <p>Registro para médicos e profissionais da saúde</p>
                </div>
            </div>
        </div>

        {{-- Formulário de Registro --}}
        <form id="registration-form" method="POST" action="{{ route('usuarios.registro') }}" style="display: none;"
            class="shadow p-4 bg-light rounded">
            @csrf

            <input type="hidden" id="tipo" name="tipo" value="">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                    value="{{ old('nome') }}" required>

            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>

            </div>

            <div class="mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" name="data_nascimento"
                    class="form-control @error('data_nascimento') is-invalid @enderror" value="{{ old('data_nascimento') }}"
                    required>

            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" id="telefone" name="telefone"
                    class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}" required>

            </div>

            <div id="usuario-fields" class="conditional-fields mb-3" style="display: none;">
                <label for="rg_usuario" class="form-label">RG</label>
                <input type="text" id="rg_usuario" name="rg_usuario"
                    class="form-control @error('rg_usuario') is-invalid @enderror" value="{{ old('rg_usuario') }}">

            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control @error('endereco') is-invalid @enderror"
                    value="{{ old('endereco') }}" required>

            </div>

            <div class="mb-3">
                <label for="plano_saude" class="form-label">Plano de Saúde</label>
                <input type="text" name="plano_saude" class="form-control @error('plano_saude') is-invalid @enderror"
                    value="{{ old('plano_saude') }}" required>

            </div>

            <div id="medico-fields" class="conditional-fields">
                <div class="mb-3">
                    <label for="crm_medico" class="form-label">CRM</label>
                    <input type="text" name="crm_medico" class="form-control @error('crm_medico') is-invalid @enderror"
                        value="{{ old('crm_medico') }}">

                </div>
                <div class="mb-3">
                    <label for="especialidade" class="form-label">Especialidade</label>
                    <input type="text" name="especialidade"
                        class="form-control @error('especialidade') is-invalid @enderror"
                        value="{{ old('especialidade') }}">

                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    required>

            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirme a Senha</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrar-se</button>
        </form>

    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cardUsuario = document.getElementById('card-usuario');
        const cardMedico = document.getElementById('card-medico');
        const registrationForm = document.getElementById('registration-form');
        const usuarioFields = document.getElementById('usuario-fields');
        const medicoFields = document.getElementById('medico-fields');
        const tipoInput = document.getElementById('tipo');

        cardUsuario.addEventListener('click', function() {
            registrationForm.style.display = 'block';
            usuarioFields.style.display = 'block';
            medicoFields.style.display = 'none';
            tipoInput.value = 'usuario';
            cardUsuario.classList.add('active');
            cardMedico.classList.remove('active');
        });

        cardMedico.addEventListener('click', function() {
            registrationForm.style.display = 'block';
            usuarioFields.style.display = 'none';
            medicoFields.style.display = 'block';
            tipoInput.value = 'medico';
            cardMedico.classList.add('active');
            cardUsuario.classList.remove('active');
        });
    });
</script>



<style>
    .tipo-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .tipo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .tipo-card.active {
        border: 2px solid #0d6efd;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .form-label {
        font-weight: bold;
    }

    .btn-primary {
        background: linear-gradient(45deg, #0d6efd, #6c757d);
        border: none;
        padding: 12px;
        border-radius: 8px;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #6c757d, #0d6efd);
    }

    .container {
        max-width: 600px;
    }

    .conditional-fields {
        display: none;
    }

    #registration-form {
        display: none;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
    }
</style>
```
- `php artisan make:view usuarios.login`
``` php
@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4 text-primary">Login</h2>
        <form method="POST" action="{{ route('usuarios.login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="font-weight-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Digite seu email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="font-weight-bold">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="Digite sua senha" required>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>

            <div class="text-center mt-3">
                <a href="" class="text-muted">Esqueceu sua senha?</a>
            </div>
        </form>
    </div>
</div>
@endsection

```
- `php artisan make:view usuarios.dashboard`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <!-- Exibir mensagens de erro -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Exibir mensagens de sucesso -->
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12 text-center">
                @if (auth()->user()->tipo == 'medico')
                    <h1 class="display-4">Bem-vindo(a), Dr(a) {{ auth()->user()->nome }}!</h1>
                    <p class="lead">Aqui estão suas consultas.</p>
                @else
                    <h1 class="display-4">Bem-vindo(a), {{ auth()->user()->nome }}!</h1>
                    <p class="lead">Confira suas consultas agendadas.</p>
                @endif
            </div>
        </div>

        <!-- Consultas do Médico ou Usuário -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                @if (auth()->user()->tipo == 'medico')
                    <h3 class="mb-0">Suas Consultas</h3>
                @else
                    <h3 class="mb-0">Consultas Agendadas</h3>
                @endif
            </div>
            <div class="card-body">
                @if ($consultas->isEmpty())
                    <p class="text-muted">Nenhuma consulta encontrada.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Observações</th>
                                    <th>{{ auth()->user()->tipo == 'medico' ? 'Paciente' : 'Médico(a)' }}</th>
                                    <th>Timer</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consultas as $consulta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($consulta->horario)->format('H:i') }}</td>
                                        <td>{{ $consulta->observacoes }}</td>
                                        <td>{{ auth()->user()->tipo == 'medico' ? $consulta->paciente->nome : $consulta->medico->nome }}
                                        </td>
                                        <td>
                                            <span class="timer badge bg-secondary"
                                                data-date-time="{{ $consulta->data }} {{ $consulta->horario }}"></span>
                                        </td>
                                        <td>
                                            <a href="{{ route('consultas.edit', $consulta->id) }}"
                                                class="btn btn-outline-warning btn-sm">Editar</a>
                                            <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-outline-danger btn-sm">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Consultas de Hoje -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Consultas de Hoje</h3>
            </div>
            <div class="card-body">
                @if ($consultasHoje->isEmpty())
                    <p class="text-muted">Nenhuma consulta agendada para hoje.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Observações</th>
                                    <th>Médico(a)</th>
                                    <th>Paciente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consultasHoje as $consulta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($consulta->horario)->format('H:i') }}</td>
                                        <td>{{ $consulta->observacoes }}</td>
                                        <td>{{ $consulta->medico->nome }}</td>
                                        <td>{{ $consulta->paciente->nome }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var timerElements = document.querySelectorAll('.timer');

            timerElements.forEach(function(timerElement) {
                var countDownDate = new Date(timerElement.dataset.dateTime).getTime();

                var x = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = countDownDate - now;

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    timerElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds +
                        "s ";

                    if (distance < 0) {
                        clearInterval(x);
                        timerElement.innerHTML = "EXPIRED";
                        timerElement.classList.remove('bg-secondary');
                        timerElement.classList.add('bg-danger');
                    }
                }, 1000);
            });
        });
    </script>
@endsection
```
- `php artisan make:view usuarios.show`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1>{{ $medico->nome }}</h1>
        <p><strong>Especialidade:</strong> {{ $medico->especialidade }}</p>
        <p><strong>CRM:</strong> {{ $medico->crm_medico }}</p>
        <p><strong>Endereço:</strong> {{ $medico->endereco }}</p>
        <p><strong>Plano de Saúde:</strong> {{ $medico->plano_saude }}</p>

        @if (session('message'))
            <p>{{ session('message') }}</p>
        @else
            <h2 class="mt-4">Meses Disponíveis</h2>
            <div>
                @foreach ($calendarios as $mesAno => $calendario)
                    <button class="btn btn-primary my-2" onclick="toggleDias('{{ $mesAno }}')">
                        {{ $calendario['mesAno']->format('F Y') }}
                    </button>

                    <div id="dias-{{ $mesAno }}" class="dias-mes" style="display: none; margin-top: 10px;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Domingo</th>
                                    <th>Segunda</th>
                                    <th>Terça</th>
                                    <th>Quarta</th>
                                    <th>Quinta</th>
                                    <th>Sexta</th>
                                    <th>Sábado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $startDay = $calendario['dias']->first()->startOfMonth()->dayOfWeek;
                                @endphp

                                <tr>
                                    @for ($i = 0; $i < $startDay; $i++)
                                        <td></td>
                                    @endfor

                                    @foreach ($calendario['dias'] as $dia)
                                        @php
                                            $isWeekend = $dia->dayOfWeek == 0 || $dia->dayOfWeek == 6;
                                        @endphp

                                        <td>
                                            @if ($isWeekend)
                                                <button class="btn btn-light disabled" disabled>
                                                    {{ $dia->day }}
                                                </button>
                                            @else
                                                <button class="btn btn-light"
                                                    onclick="toggleHorarios('{{ $dia->format('Y-m-d') }}')">
                                                    {{ $dia->day }}
                                                </button>
                                            @endif
                                            <div id="horarios-{{ $dia->format('Y-m-d') }}" class="horarios-dia"
                                                style="display: none; margin-top: 5px;">
                                                @if (isset($calendario['horarios'][$dia->format('Y-m-d')]) && count($calendario['horarios'][$dia->format('Y-m-d')]) > 0)
                                                    @foreach ($calendario['horarios'][$dia->format('Y-m-d')] as $horario)
                                                        <button class="btn btn-secondary btn-sm"
                                                            onclick="window.location.href='{{ route('consulta.create', ['data' => $dia->format('Y-m-d'), 'horario' => $horario, 'crm' => $medico->crm_medico]) }}'">
                                                            {{ $horario }}
                                                        </button>
                                                    @endforeach
                                                @else
                                                    <p>Sem horários disponíveis</p>
                                                @endif
                                            </div>
                                        </td>

                                        @if ($dia->dayOfWeek == 6)
                                </tr>
                                <tr>
                @endif
        @endforeach

        @for ($i = $calendario['dias']->last()->dayOfWeek + 1; $i <= 6; $i++)
            <td></td>
        @endfor
        </tr>
        </tbody>
        </table>
    </div>
    @endforeach
    </div>
    @endif
    </div>

    <script>
        function toggleDias(mesAno) {
            const diasDiv = document.getElementById('dias-' + mesAno);
            diasDiv.style.display = diasDiv.style.display === 'none' || diasDiv.style.display === '' ? 'block' : 'none';
        }

        function toggleHorarios(dia) {
            const horariosDiv = document.getElementById('horarios-' + dia);
            if (horariosDiv.style.display === 'none' || horariosDiv.style.display === '') {
                horariosDiv.style.display = 'flex';
                horariosDiv.style.flexDirection = 'column';
                horariosDiv.style.gap = '10px';
            } else {
                horariosDiv.style.display = 'none';
                horariosDiv.style.flexDirection = '';
                horariosDiv.style.gap = '';
            }
        }
    </script>
@endsection
```
- `php artisan make:view usuarios.medicos`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1 class="mb-4">Lista de Médicos</h1>

        {{-- Pesquisa --}}
        <form action="{{ route('medicos.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="nome" placeholder="Nome"
                            value="{{ request('nome') }}">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                        <select class="form-control" name="especialidade">
                            <option value="">Especialidades</option>
                            @foreach ($especialidades as $especialidade)
                                <option value="{{ $especialidade }}"
                                    {{ request('especialidade') == $especialidade ? 'selected' : '' }}>
                                    {{ $especialidade }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-hospital"></i></span>
                        <input type="text" class="form-control" name="plano_saude" placeholder="Plano de Saúde"
                            value="{{ request('plano_saude') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                </div>
            </div>
        </form>

        {{-- Médicos --}}
        <div class="row">
            @foreach ($medicos as $medico)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $medico->nome }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $medico->especialidade }}</h6>
                            <p class="card-text"><strong>CRM:</strong> {{ $medico->crm_medico }}</p>
                            <p class="card-text"><strong>Localização:</strong> {{ $medico->endereco }}</p>
                            <p class="card-text"><strong>Plano de Saúde:</strong> {{ $medico->plano_saude }}</p>
                            <a href="{{ route('usuarios.show', ['id' => $medico->id]) }}" class="btn btn-primary w-100">Ver
                                Horários Disponíveis</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

<style>
    .input-group-text {
        background-color: #f8f9fa;
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>
```

Criar o **DashboardController**
- `php artisan make:controller DashboardController`
``` php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consulta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
    
        if ($usuario->tipo == 'medico') {
            $consultas = Consulta::where('crm_medico', $usuario->crm_medico)
                                 ->with('medico')
                                 ->get();
            $consultasHoje = Consulta::where('crm_medico', $usuario->crm_medico)
                                     ->whereDate('data', Carbon::today()) 
                                     ->with('medico')
                                     ->get();
        } else {
            $consultas = Consulta::where('rg_usuario', $usuario->rg_usuario)
                                 ->with('paciente')
                                 ->get();
            $consultasHoje = Consulta::where('rg_usuario', $usuario->rg_usuario)
                                     ->whereDate('data', Carbon::today()) 
                                     ->with('paciente')
                                     ->get();
        }
    
        return view('usuarios.dashboard', compact('consultas', 'consultasHoje'));
    }
    
}
```

Criar o **AgendamentoController:**
- `php artisan make:controller AgendamentoController`
``` php
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


    public function edit($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        return view('agendamentos.edit', compact('agendamento'));
    }

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
```

Criar as **Views** para Agendamentos:
- `php artisan make:view agendamentos.create`
``` php
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Novo Agendamento</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agendamentos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="turno" class="form-label">Turno</label>
                <select name="turno" id="turno" class="form-control">
                    <option value="Manhã">Manhã</option>
                    <option value="Tarde">Tarde</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="mes" class="form-label">Mês</label>
                <input type="month" name="mes" id="mes" class="form-control">
            </div>
            <div class="mb-3">
                <label for="nome_medico" class="form-label">Nome do Médico</label>
                <input type="text" name="nome_medico" class="form-control" id="nome_medico" value="{{ auth()->user()->nome }}" readonly>
            </div>
            <div class="mb-3">
                <label for="endereco_consultorio" class="form-label">Endereço do Consultório</label>
                <input type="text" name="endereco_consultorio" class="form-control" id="endereco_consultorio" value="{{ auth()->user()->endereco }}">
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" name="preco" class="form-control" id="preco">
            </div>
            <div class="mb-3">
                <label for="modalidade" class="form-label">Modalidade</label>
                <select name="modalidade" id="modalidade" class="form-control">
                    <option value="Presencial">Presencial</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="especialidade" class="form-label">Especialidade</label>
                <input type="text" name="especialidade" class="form-control" id="especialidade" value="{{ auth()->user()->especialidade }}" readonly>
            </div>
            <div class="mb-3">
                <label for="crm_medico" class="form-label">CRM do Médico</label>
                <input type="text" name="crm_medico" class="form-control" id="crm_medico" value="{{ auth()->user()->crm_medico }}" readonly>
            </div>
            <div id="calendar-container"></div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
    @endsection
```

- `php artisan make:view agendamentos.edit`
``` php
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Agendamento</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agendamentos.update', $agendamento->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="turno">Turno</label>
                <input type="text" class="form-control" id="turno" name="turno" value="{{ $agendamento->turno }}"
                    required>
            </div>

            <div class="form-group">
                <label for="nome_medico">Nome do Médico</label>
                <input type="text" class="form-control" id="nome_medico" name="nome_medico"
                    value="{{ $agendamento->nome_medico }}" required>
            </div>

            <div class="form-group">
                <label for="mes">Mês</label>
                <input type="text" class="form-control" id="mes" name="mes" value="{{ $agendamento->mes }}"
                    required>
            </div>

            <div class="form-group">
                <label for="endereco_consultorio">Endereço do Consultório</label>
                <input type="text" class="form-control" id="endereco_consultorio" name="endereco_consultorio"
                    value="{{ $agendamento->endereco_consultorio }}" required>
            </div>

            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco"
                    value="{{ $agendamento->preco }}" required>
            </div>

            <div class="form-group">
                <label for="modalidade">Modalidade</label>
                <input type="text" class="form-control" id="modalidade" name="modalidade"
                    value="{{ $agendamento->modalidade }}" required>
            </div>

            <div class="form-group">
                <label for="especialidade">Especialidade</label>
                <input type="text" class="form-control" id="especialidade" name="especialidade"
                    value="{{ $agendamento->especialidade }}" required>
            </div>

            <div class="form-group">
                <label for="crm_medico">CRM do Médico</label>
                <input type="text" class="form-control" id="crm_medico" name="crm_medico"
                    value="{{ $agendamento->crm_medico }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
@endsection
```

- `php artisan make:view agendamentos.index`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1 class="mb-4">Agendamentos</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <a href="{{ route('agendamentos.create') }}" class="btn btn-primary mb-3">
            Novo Agendamento
        </a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Turno</th>
                        <th>Mês</th>
                        <th>Nome do Médico</th>
                        <th>Endereço</th>
                        <th>Preço</th>
                        <th>Modalidade</th>
                        <th>Especialidade</th>
                        <th>CRM</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agendamentos as $agendamento)
                        <tr>
                            <td>{{ $agendamento->id }}</td>
                            <td>{{ $agendamento->turno }}</td>
                            <td>{{ $agendamento->mes }}</td>
                            <td>{{ $agendamento->nome_medico }}</td>
                            <td>{{ $agendamento->endereco_consultorio }}</td>
                            <td>{{ $agendamento->preco }}</td>
                            <td>{{ $agendamento->modalidade }}</td>
                            <td>{{ $agendamento->especialidade }}</td>
                            <td>{{ $agendamento->crm_medico }}</td>
                            <td class="text-center">
                                <a href="{{ route('agendamentos.edit', $agendamento->id) }}"
                                    class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <form action="{{ route('agendamentos.destroy', $agendamento->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
```

Criar o **Middleware** de Agendamentos:
- `php artisan make:middleware AgendamentoMiddleware`
``` php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AgendamentoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->tipo === 'medico') {
            return $next($request);
        }
        return redirect()->route('login')->withErrors(['access' => 'Você não tem permissão para acessar essa área.']);
    }
}

```


Criar o **Controller** de Consultas:
- `php artisan make:controller ConsultaController` 
``` php
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
```

Criar as **Views** para Consultas:
- `php artisan make:view consultas.create`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm p-4">
            <h1 class="text-center mb-4 text-primary">Agendar Consulta</h1>

            <!-- Exibir mensagens de erro -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erro!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Exibir mensagens de sucesso -->
            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sucesso!</strong> {{ session('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('consulta.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="data" class="font-weight-bold">Data:</label>
                        <input type="text" class="form-control" id="data" name="data" value="{{ $data }}"
                            readonly>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="horario" class="font-weight-bold">Horário:</label>
                        <input type="text" class="form-control" id="horario" name="horario" value="{{ $horario }}"
                            readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="crm" class="font-weight-bold">CRM Médico:</label>
                    <input type="text" class="form-control" id="crm" name="crm" value="{{ $crm }}"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="rg_usuario" class="font-weight-bold">RG do Usuário:</label>
                    <input type="text" class="form-control" id="rg_usuario" name="rg_usuario"
                        value="{{ auth()->user()->rg_usuario }}" readonly>
                </div>

                <div class="form-group">
                    <label for="observacoes" class="font-weight-bold">Observações:</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4"
                        placeholder="Escreva aqui suas observações..."></textarea>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Agendar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
```

- `php artisan make:view consultas.create`
``` php
@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1>Editar Consulta</h1>

        <form action="{{ route('consulta.update', $consulta->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="text" class="form-control" id="data" name="data" value="{{ $consulta->data }}" required>
            </div>

            <div class="form-group">
                <label for="horario">Horário:</label>
                <input type="text" class="form-control" id="horario" name="horario" value="{{ $consulta->horario }}"
                    required>
            </div>

            <div class="form-group">
                <label for="crm">CRM Médico:</label>
                <input type="text" class="form-control" id="crm" name="crm" value="{{ $consulta->crm_medico }}"
                    readonly required>
            </div>

            <div class="form-group">
                <label for="rg_usuario">RG do Usuário:</label>
                <input type="text" class="form-control" id="rg_usuario" name="rg_usuario"
                    value="{{ $consulta->rg_usuario }}" readonly required>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea class="form-control" id="observacoes" name="observacoes">{{ $consulta->observacoes }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
@endsection
```





Definir as rotas em `routes/web.php:`
``` php
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


## Escopo

`Escopo:`
```Desenvolver uma plataforma online para conectar médicos e pacientes. Médicos poderão se cadastrar, cadastrar horários disponíveis para consultas, definir preços e modalidades, enquanto pacientes poderão se cadastrar , buscar médicos, visualizar horários e agendar consultas.
Esta solução visa simplificar e otimizar o processo de agendamento de consultas, oferecendo uma interface intuitiva e funcional para ambos os lados.
```

Objetivos:
```
Desenvolver uma plataforma online para conectar médicos e pacientes, permitindo o cadastro e gerenciamento de horários e consultas, dentro de um prazo de 5 meses.
```

Específicos:
```
Desenvolver e Implementar o Sistema de Cadastro de Usuários e Médicos.
Desenvolver Funcionalidades de Gestão de Horários e Agendamentos.
Criar uma Interface de Usuário Intuitiva e Responsiva.
Implementar Sistema de Testes e Garantia de Qualidade.
Realizar o Deploy e Lançamento da Plataforma.
```

Mensuráveis:
```
Cadastro e Login.
Gestão de Horários e Consultas.
Interface de Usuário.
Qualidade e Testes.
Deploy e Lançamento.
```


Atingíveis:
```
Utilizar sistemas de autenticação padrão e bibliotecas confiáveis do Laravel para garantir um sistema seguro e funcional.
Implementar funcionalidades utilizando recursos já existentes e testados do Laravel, garantindo a entrega dentro do prazo.
Utilizar frameworks e bibliotecas front-end para criar uma interface responsiva e intuitiva dentro dos requisitos estabelecidos.
Alocar tempo suficiente para testes e correções de bugs, com a colaboração do QA e desenvolvedores para garantir a qualidade.
Utilizar ferramentas e práticas de CI/CD para garantir um deploy eficiente e seguro.
```

Relevantes:
```
Cadastro e Login: Essencial para garantir que médicos e pacientes possam usar a plataforma de forma segura e personalizada.
Interface de Usuário (UX/UI): Crucial para a experiência do usuário. Uma interface amigável aumenta a usabilidade.
Gestão de Horários e Consultas: Fundamental para a funcionalidade principal da plataforma, permitindo que os usuários interajam efetivamente.
Testes: Garante que a plataforma funcione conforme o esperado, minimizando problemas.
Deploy: Garantir que a plataforma esteja disponível e funcional para todos os usuários finais.
```

Temporais:
```
Planejamento e Design: Finalizar o desenvolvimento e testes até o final do Mês 1.
Desenvolvimento Back-End: Completar o desenvolvimento e integração até o final do Mês 2.
Interface de Usuário: Finalizar o design e a implementação até o final do Mês 3.
Integração e Testes: Concluir a fase de testes e ajustes até o final do Mês 4.
Deploy e Lançamento: Configurar o ambiente de produção e realizar o lançamento até o final do Mês 5.
```

Cronograma:
```
Mês 1: Planejamento e Design
Definição de Escopo e Objetivos:
Reuniões com stakeholders para definir escopo, objetivos e requisitos.
Elaboração de documentos de requisitos e planejamento.
Design da Arquitetura e Protótipos:
Desenvolvimento da arquitetura do sistema.
Criação de protótipos de média e alta fidelidade.
Definição de tecnologias e planejamento da infraestrutura.
```

```
Mês 2: Desenvolvimento do Back-End
Configuração do Ambiente de Desenvolvimento e Funcionalidades Básicas:
Implementação de autenticação e gerenciamento de usuários.
Funcionalidades Avançadas do Back-End:
Desenvolvimento de APIs para gerenciamento de horários e consultas.
Integração com o banco de dados.
```

```
Mês 3: Desenvolvimento do Front-End
Desenvolvimento das Páginas Principais (cadastro, login, home).
Criação das Telas para Visualização e Busca de Médicos (pacientes).
Implementação das Páginas de Agendamento de Consultas (pacientes).
Desenvolvimento das Telas para Médicos Publicarem e Gerenciarem Horários (médicos).
```

```
Mês 4: Integração e Testes
Integração Completa entre Front-End e Back-End.
Realização de Testes e Correção de Bugs.
Condução de Testes de Usabilidade com Usuários Reais (feedback).
Coleta de Feedback e Realização de Ajustes.
```

```
Mês 5: Finalização e Lançamento
Implementação de Melhorias Finais e Correção de Problemas Encontrados.
Preparação de Documentação e Material de Suporte.
Lançamento da Aplicação/Deploy.
Monitoramento de Desempenho e Suporte.
```

Recursos **(Ferramentas)**:
```
JIRA (Organização).
VSCode (Desenvolvimento).
Figma/Adobe (Design).
Postman (Testes de API).
GitLab (Controle de Versão e CI/CD).
Google Meet (Pro) (Comunicação).
PostgreSQL (Banco de Dados).
```

Recursos **(Desenvolvedores)**:
```
Gerente de Projetos.
Desenvolvedor Back-End (PHP/Laravel) - Pleno.
Desenvolvedor Front-End / Designer - Pleno.
DBA (Banco de Dados) - Pleno.
QA (Qualidade de Software) - Pleno.
Especialista em SI (Cybersecurity).
Estagiário em DEV (Documentação).
```

Análise de Riscos:
```
O projeto pode enfrentar atrasos, mudanças nos requisitos ou problemas imprevistos.
Adoção de metodologias ágeis como Scrum e Kanban, com reuniões frequentes.
Inclusão de um cronograma mais abrangente.
Elaboração de plano B, C...
Comunicação clara.

Pode haver problemas técnicos, bugs e falhas que afetam a funcionalidade e a experiência do usuário.
Testes contínuos, revisões de código, monitoramento de falhas.

Vulnerabilidades de segurança podem expor dados sensíveis ou permitir acessos não autorizados.
Testes de segurança, atualizações constantes, controle de acesso.

A plataforma pode enfrentar problemas de desempenho ou escalabilidade à medida que o número de usuários cresce.
Testes e otimização de recursos.
```

## Diagramas
Diagrama de **Classe**:
![Diagrama de Classe](z_documentacao\diagrama_classes.png)


Diagrama de **Fluxo**:
![Diagrama de Fluxo](z_documentacao\diagrama_fluxo.png)

Diagrama de **Uso**:
![Diagrama de Uso](z_documentacao\diagrama_uso.png)

