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
                    <input type="text" class="form-control" name="nome" placeholder="Nome" value="{{ request('nome') }}">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                    <select class="form-control" name="especialidade">
                        <option value="">Especialidades</option>
                        @foreach ($especialidades as $especialidade)
                            <option value="{{ $especialidade }}" {{ request('especialidade') == $especialidade ? 'selected' : '' }}>
                                {{ $especialidade }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-hospital"></i></span>
                    <input type="text" class="form-control" name="plano_saude" placeholder="Plano de Saúde" value="{{ request('plano_saude') }}">
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
                        <a href="{{ route('usuarios.show', ['id' => $medico->id]) }}" class="btn btn-primary w-100">Ver Horários Disponíveis</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

<style>
    .input-group-text {
        background-color: #f8f9fa; /* Light background for icons */
    }

    .card-img-top {
        height: 200px;
        object-fit: cover; /* Ensures the image covers the area */
    }

    .card {
        border-radius: 0.5rem; /* Rounded corners for cards */
        overflow: hidden; /* Ensures that child elements are contained */
    }
</style>
