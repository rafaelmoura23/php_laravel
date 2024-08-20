@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>{{ $medico->nome }}</h1>
    <p><strong>Especialidade:</strong> {{ $medico->especialidade }}</p>
    <p><strong>CRM:</strong> {{ $medico->crm_medico }}</p>
    <p><strong>Endereço:</strong> {{ $medico->endereco }}</p>
    <p><strong>Plano de Saúde:</strong> {{ $medico->plano_saude }}</p>
    
    <h2 class="mt-4">Agendamentos</h2>
    @if ($medico->agendamentos->isEmpty())
        <p>Não há agendamentos para este médico.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Turno</th>
                    <th>Endereço do Consultório</th>
                    <th>Mês</th>
                    <th>Preço</th>
                    <th>Modalidade</th>
                    <th>Especialidade</th>
                    <th>CRM Médico</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medico->agendamentos as $agendamento)
                    <tr>
                        <td>{{ $agendamento->turno }}</td>
                        <td>{{ $agendamento->endereco_consultorio }}</td>
                        <td>{{ $agendamento->mes }}</td>
                        <td>{{ $agendamento->preco }}</td>
                        <td>{{ $agendamento->modalidade }}</td>
                        <td>{{ $agendamento->especialidade }}</td>
                        <td>{{ $agendamento->crm_medico }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
