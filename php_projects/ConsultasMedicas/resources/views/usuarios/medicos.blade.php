@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Lista de Médicos</h1>

    <!-- Formulário de Pesquisa -->
    <form action="{{ route('medicos.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <input type="text" class="form-control" name="nome" placeholder="Nome" value="{{ request('nome') }}">
            </div>
            <div class="col-md-4 mb-3 custom-select">
                <select class="form-control" name="especialidade">
                    <option value="">Todos</option>
                    @foreach ($especialidades as $especialidade)
                        <option value="{{ $especialidade }}" {{ request('especialidade') == $especialidade ? 'selected' : '' }}>
                            {{ $especialidade }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <input type="text" class="form-control" name="plano_saude" placeholder="Plano de Saúde" value="{{ request('plano_saude') }}">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </div>
        </div>
    </form>

    <!-- Cards de Médicos -->
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
                        <a href="{{ route('usuarios.show', ['id' => $medico->id]) }}" class="btn btn-primary">Ver Horários Disponíveis</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

<style>
    .custom-select {
        position: relative;
    }

    .custom-select select {
        appearance: none; /* Remove o estilo padrão do navegador */
        background: #fff url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="16" height="16"%3E%3Cpath fill="none" stroke="%23000" stroke-width="40" d="M112 184l64 64 64-64" /%3E%3C/svg>') no-repeat right 10px center; /* Adiciona a seta ao lado direito */
        background-size: 16px; /* Tamanho da seta */
        padding-right: 30px; /* Espaço para a seta */
        border: 1px solid #ced4da; /* Estilo da borda */
        border-radius: 0.25rem; /* Arredondamento dos cantos */
    }

    .custom-select select:focus {
        outline: none; /* Remove o contorno de foco padrão */
        border-color: #80bdff; /* Cor da borda quando focado */
    }
</style>