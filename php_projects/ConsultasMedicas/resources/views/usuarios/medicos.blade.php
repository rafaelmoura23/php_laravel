@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Lista de Médicos</h1>

    <!-- Cards de Médicos -->
    <div class="row">
        @foreach ($medicos as $medico)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="{{ asset('assets/img/doctor_placeholder.png') }}" class="card-img-top" alt="{{ $medico->nome }}">
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
