@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>{{ $medico->nome }}</h1>
    <p><strong>Especialidade:</strong> {{ $medico->especialidade }}</p>
    <p><strong>CRM:</strong> {{ $medico->crm_medico }}</p>
    <p><strong>Endereço:</strong> {{ $medico->endereco }}</p>
    <p><strong>Plano de Saúde:</strong> {{ $medico->plano_saude }}</p>
    <!-- Adicione mais detalhes conforme necessário -->
</div>
@endsection
