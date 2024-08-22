@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Exibir mensagens de erro -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Exibir mensagens de sucesso -->
    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12 text-center">
            @if (auth()->user()->tipo == 'medico')
                <h1 class="display-4">Bem-vindo(a), Dr(a) {{ auth()->user()->nome }}!</h1>
                <p class="lead">Aqui estão suas consultas.</p>
            @else
                <h1 class="display-4">Bem-vindo(a), {{ auth()->user()->nome }}!</h1>
                <p class="lead">Confira suas consultas agendadas.</p>
            @endif
        </div>
    </div>

    <!-- Consultas do Médico ou Usuário -->
    <div class="card mb-5">
        <div class="card-header bg-primary text-white">
            @if (auth()->user()->tipo == 'medico')
                <h3 class="mb-0">Suas Consultas</h3>
            @else
                <h3 class="mb-0">Consultas Agendadas</h3>
            @endif
        </div>
        <div class="card-body">
            @if ($consultas->isEmpty())
                <p class="text-muted">Nenhuma consulta encontrada.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Observações</th>
                                <th>{{ auth()->user()->tipo == 'medico' ? 'Paciente' : 'Médico(a)' }}</th>
                                <th>Timer</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consultas as $consulta)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($consulta->horario)->format('H:i') }}</td>
                                    <td>{{ $consulta->observacoes }}</td>
                                    <td>{{ auth()->user()->tipo == 'medico' ? $consulta->paciente->nome : $consulta->medico->nome }}</td>
                                    <td>
                                        <span class="timer badge bg-secondary" data-date-time="{{ $consulta->data }} {{ $consulta->horario }}"></span>
                                    </td>
                                    <td>
                                        <a href="{{ route('consultas.edit', $consulta->id) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                                        <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Consultas de Hoje -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">Consultas de Hoje</h3>
        </div>
        <div class="card-body">
            @if ($consultasHoje->isEmpty())
                <p class="text-muted">Nenhuma consulta agendada para hoje.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Observações</th>
                                <th>Médico(a)</th>
                                <th>Paciente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consultasHoje as $consulta)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($consulta->data)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($consulta->horario)->format('H:i') }}</td>
                                    <td>{{ $consulta->observacoes }}</td>
                                    <td>{{ $consulta->medico->nome }}</td>
                                    <td>{{ $consulta->paciente->nome }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var timerElements = document.querySelectorAll('.timer');

        timerElements.forEach(function (timerElement) {
            var countDownDate = new Date(timerElement.dataset.dateTime).getTime();

            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    timerElement.innerHTML = "EXPIRED";
                    timerElement.classList.remove('bg-secondary');
                    timerElement.classList.add('bg-danger');
                }
            }, 1000);
        });
    });
</script>
@endsection
