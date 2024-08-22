@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1>{{ $medico->nome }}</h1>
        <p><strong>Especialidade:</strong> {{ $medico->especialidade }}</p>
        <p><strong>CRM:</strong> {{ $medico->crm_medico }}</p>
        <p><strong>Endereço:</strong> {{ $medico->endereco }}</p>
        <p><strong>Plano de Saúde:</strong> {{ $medico->plano_saude }}</p>

        @if (session('message'))
            <p>{{ session('message') }}</p>
        @else
            <h2 class="mt-4">Meses Disponíveis</h2>
            <div>
                @foreach ($calendarios as $mesAno => $calendario)
                    <button class="btn btn-primary my-2" onclick="toggleDias('{{ $mesAno }}')">
                        {{ $calendario['mesAno']->format('F Y') }}
                    </button>

                    <div id="dias-{{ $mesAno }}" class="dias-mes" style="display: none; margin-top: 10px;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Domingo</th>
                                    <th>Segunda</th>
                                    <th>Terça</th>
                                    <th>Quarta</th>
                                    <th>Quinta</th>
                                    <th>Sexta</th>
                                    <th>Sábado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $startDay = $calendario['dias']->first()->startOfMonth()->dayOfWeek;
                                @endphp

                                <tr>
                                    @for ($i = 0; $i < $startDay; $i++)
                                        <td></td>
                                    @endfor

                                    @foreach ($calendario['dias'] as $dia)
                                        @php
                                            $isWeekend = $dia->dayOfWeek == 0 || $dia->dayOfWeek == 6;
                                        @endphp

                                        <td>
                                            @if ($isWeekend)
                                                <button class="btn btn-light disabled" disabled>
                                                    {{ $dia->day }}
                                                </button>
                                            @else
                                                <button class="btn btn-light"
                                                    onclick="toggleHorarios('{{ $dia->format('Y-m-d') }}')">
                                                    {{ $dia->day }}
                                                </button>
                                            @endif
                                            <div id="horarios-{{ $dia->format('Y-m-d') }}" class="horarios-dia"
                                                style="display: none; margin-top: 5px;">
                                                @if (isset($calendario['horarios'][$dia->format('Y-m-d')]) && count($calendario['horarios'][$dia->format('Y-m-d')]) > 0)
                                                    @foreach ($calendario['horarios'][$dia->format('Y-m-d')] as $horario)
                                                        <button class="btn btn-secondary btn-sm"
                                                            onclick="window.location.href='{{ route('consulta.create', ['data' => $dia->format('Y-m-d'), 'horario' => $horario, 'crm' => $medico->crm_medico]) }}'">
                                                            {{ $horario }}
                                                        </button>
                                                    @endforeach
                                                @else
                                                    <p>Sem horários disponíveis</p>
                                                @endif
                                            </div>
                                        </td>

                                        @if ($dia->dayOfWeek == 6)
                                            </tr>
                                            <tr>
                                        @endif
                                    @endforeach

                                    @for ($i = $calendario['dias']->last()->dayOfWeek + 1; $i <= 6; $i++)
                                        <td></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function toggleDias(mesAno) {
            const diasDiv = document.getElementById('dias-' + mesAno);
            diasDiv.style.display = diasDiv.style.display === 'none' || diasDiv.style.display === '' ? 'block' : 'none';
        }

        function toggleHorarios(dia) {
    const horariosDiv = document.getElementById('horarios-' + dia);
    if (horariosDiv.style.display === 'none' || horariosDiv.style.display === '') {
        horariosDiv.style.display = 'flex';
        horariosDiv.style.flexDirection = 'column';
        horariosDiv.style.gap = '10px'; // Espaço entre os botões
    } else {
        horariosDiv.style.display = 'none';
        horariosDiv.style.flexDirection = '';
        horariosDiv.style.gap = ''; // Remove o espaço
    }
}
    </script>
@endsection


