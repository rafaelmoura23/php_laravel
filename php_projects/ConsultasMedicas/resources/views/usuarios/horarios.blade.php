@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Horários Disponíveis</h1>

    <!-- Formulário de Filtro -->
    <form method="GET" action="{{ route('horarios') }}" class="mb-4">
        <div class="form-group">
            <label for="mes">Mês:</label>
            <select name="mes" id="mes" class="form-control">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label for="turno">Turno:</label>
            <select name="turno" id="turno" class="form-control">
                <option value="manhã" {{ $turno == 'manhã' ? 'selected' : '' }}>Manhã</option>
                <option value="tarde" {{ $turno == 'tarde' ? 'selected' : '' }}>Tarde</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
    </form>

    <!-- Calendário -->
    <div class="row">
        @foreach (range(1, Carbon::create()->month($mes)->daysInMonth) as $dia)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Dia {{ $dia }}</h5>
                        @foreach ($horarios as $horario)
                            <p class="card-text">{{ $horario }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
