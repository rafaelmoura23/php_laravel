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
            <input type="text" class="form-control" id="horario" name="horario" value="{{ $consulta->horario }}" required>
        </div>

        <div class="form-group">
            <label for="crm">CRM Médico:</label>
            <input type="text" class="form-control" id="crm" name="crm" value="{{ $consulta->crm_medico }}" readonly required>
        </div>

        <div class="form-group">
            <label for="rg_usuario">RG do Usuário:</label>
            <input type="text" class="form-control" id="rg_usuario" name="rg_usuario" value="{{ $consulta->rg_usuario }}" readonly required>
        </div>

        <div class="form-group">
            <label for="observacoes">Observações:</label>
            <textarea class="form-control" id="observacoes" name="observacoes">{{ $consulta->observacoes }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection
