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

