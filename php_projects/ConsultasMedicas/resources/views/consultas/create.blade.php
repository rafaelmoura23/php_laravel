@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h1 class="text-center mb-4 text-primary">Agendar Consulta</h1>

        <!-- Exibir mensagens de erro -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erro!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Exibir mensagens de sucesso -->
        @if(session('message'))
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
                    <input type="text" class="form-control" id="data" name="data" value="{{ $data }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="horario" class="font-weight-bold">Horário:</label>
                    <input type="text" class="form-control" id="horario" name="horario" value="{{ $horario }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="crm" class="font-weight-bold">CRM Médico:</label>
                <input type="text" class="form-control" id="crm" name="crm" value="{{ $crm }}" readonly>
            </div>

            <div class="form-group">
                <label for="rg_usuario" class="font-weight-bold">RG do Usuário:</label>
                <input type="text" class="form-control" id="rg_usuario" name="rg_usuario" value="{{ auth()->user()->rg_usuario }}" readonly>
            </div>

            <div class="form-group">
                <label for="observacoes" class="font-weight-bold">Observações:</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="4" placeholder="Escreva aqui suas observações..."></textarea>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Agendar</button>
            </div>
        </form>
    </div>
</div>
@endsection
