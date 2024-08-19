@extends('layouts.app')

@section('content')
    {{-- formulário --}}
    <div class="container">
        <h1>Registrar-se</h1>

        {{-- Seleção Inicial do Tipo --}}
        <div id="tipo-selection" class="mb-4">
            <h2>Qual o seu tipo?</h2>
            <button id="btn-usuario" class="btn btn-primary">Usuário</button>
            <button id="btn-medico" class="btn btn-secondary">Médico</button>
        </div>

        {{-- Formulário de Registro --}}
        <form id="registration-form" method="POST" action="{{ route('usuarios.registro') }}" style="display: none;">
            @csrf

            <input type="hidden" id="tipo" name="tipo" value="">

            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" name="data_nascimento" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" name="telefone" class="form-control" required>
            </div>

            <div id="usuario-fields" class="conditional-fields" style="display: none;">
                <div class="form-group">
                    <label for="rg_usuario">RG</label>
                    <input type="text" name="rg_usuario" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" name="endereco" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="plano_saude">Plano de Saúde</label>
                <input type="text" name="plano_saude" class="form-control" required>
            </div>

            <div id="medico-fields" class="conditional-fields" style="display: none;">
                <div class="form-group">
                    <label for="crm_medico">CRM</label>
                    <input type="text" name="crm_medico" class="form-control">
                </div>
                <div class="form-group">
                    <label for="especialidade">Especialidade</label>
                    <input type="text" name="especialidade" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirme a Senha</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrar-se</button>
        </form>
    </div>
@endsection


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnUsuario = document.getElementById('btn-usuario');
            const btnMedico = document.getElementById('btn-medico');
            const tipoSelection = document.getElementById('tipo-selection');
            const registrationForm = document.getElementById('registration-form');
            const usuarioFields = document.getElementById('usuario-fields');
            const medicoFields = document.getElementById('medico-fields');
            const tipoInput = document.getElementById('tipo');

            btnUsuario.addEventListener('click', function() {
                tipoSelection.style.display = 'none';
                registrationForm.style.display = 'block';
                usuarioFields.style.display = 'block';
                medicoFields.style.display = 'none';
                tipoInput.value = 'usuario';
            });

            btnMedico.addEventListener('click', function() {
                tipoSelection.style.display = 'none';
                registrationForm.style.display = 'block';
                usuarioFields.style.display = 'none';
                medicoFields.style.display = 'block';
                tipoInput.value = 'medico';
            });
        });
    </script>

