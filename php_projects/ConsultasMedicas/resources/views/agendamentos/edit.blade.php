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
            <input type="text" class="form-control" id="turno" name="turno" value="{{ $agendamento->turno }}" required>
        </div>

        <div class="form-group">
            <label for="nome_medico">Nome do Médico</label>
            <input type="text" class="form-control" id="nome_medico" name="nome_medico" value="{{ $agendamento->nome_medico }}" required>
        </div>

        <div class="form-group">
            <label for="mes">Mês</label>
            <input type="text" class="form-control" id="mes" name="mes" value="{{ $agendamento->mes }}" required>
        </div>

        <div class="form-group">
            <label for="endereco_consultorio">Endereço do Consultório</label>
            <input type="text" class="form-control" id="endereco_consultorio" name="endereco_consultorio" value="{{ $agendamento->endereco_consultorio }}" required>
        </div>

        <div class="form-group">
            <label for="preco">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="{{ $agendamento->preco }}" required>
        </div>

        <div class="form-group">
            <label for="modalidade">Modalidade</label>
            <input type="text" class="form-control" id="modalidade" name="modalidade" value="{{ $agendamento->modalidade }}" required>
        </div>

        <div class="form-group">
            <label for="especialidade">Especialidade</label>
            <input type="text" class="form-control" id="especialidade" name="especialidade" value="{{ $agendamento->especialidade }}" required>
        </div>

        <div class="form-group">
            <label for="crm_medico">CRM do Médico</label>
            <input type="text" class="form-control" id="crm_medico" name="crm_medico" value="{{ $agendamento->crm_medico }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
@endsection
