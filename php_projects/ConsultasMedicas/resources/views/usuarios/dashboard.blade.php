@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Exibir mensagens de erro -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Exibir mensagens de sucesso -->
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (auth()->user()->tipo == 'medico')
        <h1>Bem-vindo(a), Dr(a) {{ auth()->user()->nome }}!</h1>
        <h2>Suas Consultas</h2>
        @if ($consultas->isEmpty())
            <p>Nenhuma consulta encontrada.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Observações</th>
                        <th>Paciente</th>
                        <th>Timer</th> <!-- Nova coluna para o timer -->
                        <th>Ações</th> <!-- Coluna para ações -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consultas as $consulta)
                        <tr>
                            <td>{{ $consulta->data }}</td>
                            <td>{{ $consulta->horario }}</td>
                            <td>{{ $consulta->observacoes }}</td>
                            <td>{{ $consulta->paciente->nome }}</td>
                            <td>
                                <span 
                                    class="timer" 
                                    data-date-time="{{ $consulta->data }} {{ $consulta->horario }}"></span>
                            </td> <!-- Timer -->
                            <td>
                                <a href="{{ route('consultas.edit', $consulta->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <h1>Bem-vindo(a), {{ auth()->user()->nome }}!</h1>
        <h2>Consultas Agendadas</h2>
        @if ($consultas->isEmpty())
            <p>Nenhuma consulta encontrada.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Observações</th>
                        <th>Médico(a)</th>
                        <th>Timer</th> <!-- Nova coluna para o timer -->
                        <th>Ações</th> <!-- Coluna para ações -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consultas as $consulta)
                        <tr>
                            <td>{{ $consulta->data }}</td>
                            <td>{{ $consulta->horario }}</td>
                            <td>{{ $consulta->observacoes }}</td>
                            <td>{{ $consulta->medico->nome }}</td>
                            <td>
                                <span 
                                    class="timer" 
                                    data-date-time="{{ $consulta->data }} {{ $consulta->horario }}"></span>
                            </td> <!-- Timer -->
                            <td>
                                <a href="{{ route('consultas.edit', $consulta->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    <h2>Consultas de Hoje</h2>
    @php
        $hoje = \Carbon\Carbon::now()->format('Y-m-d');
    @endphp
    @if ($consultasHoje->isEmpty())
        <p>Nenhuma consulta agendada para hoje.</p>
    @else
        <table class="table table-striped">
            <thead>
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
                        <td>{{ $consulta->data }}</td>
                        <td>{{ $consulta->horario }}</td>
                        <td>{{ $consulta->observacoes }}</td>
                        <td>{{ $consulta->medico->nome }}</td>
                        <td>{{ $consulta->paciente->nome }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleciona todos os elementos com a classe 'timer'
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
                }
            }, 1000);
        });
    });
</script>
@endsection
