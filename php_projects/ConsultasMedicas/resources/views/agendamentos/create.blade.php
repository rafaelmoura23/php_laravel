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