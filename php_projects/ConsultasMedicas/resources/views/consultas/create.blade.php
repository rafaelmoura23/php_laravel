@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>Agendar Consulta</h1>

    <!-- Exibir mensagens de erro -->
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Exibir mensagens de sucesso -->
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ route('consulta.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="data">Data:</label>
            <input type="text" class="form-control" id="data" name="data" value="{{ $data }}" readonly>
        </div>

        <div class="form-group">
            <label for="horario">Horário:</label>
            <input type="text" class="form-control" id="horario" name="horario" value="{{ $horario }}" readonly>
        </div>

        <div class="form-group">
            <label for="crm">CRM Médico:</label>
            <input type="text" class="form-control" id="crm" name="crm" value="{{ $crm }}" readonly>
        </div>

        <div class="form-group">
            <label for="rg_usuario">RG do Usuário:</label>
            <input type="text" class="form-control" id="rg_usuario" name="rg_usuario" value="{{ auth()->user()->rg_usuario }}" readonly>
        </div>

        <div class="form-group">
            <label for="observacoes">Observações:</label>
            <textarea class="form-control" id="observacoes" name="observacoes"></textarea>
        </div>

        {{-- <input type="hidden" name="id_agendamento" value="{{ $id_agendamento }}"> --}}

        <button type="submit" class="btn btn-primary">Agendar</button>
    </form>
</div>

@endsection
