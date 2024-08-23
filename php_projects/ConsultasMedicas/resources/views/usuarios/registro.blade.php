@extends('layouts.app')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>


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
