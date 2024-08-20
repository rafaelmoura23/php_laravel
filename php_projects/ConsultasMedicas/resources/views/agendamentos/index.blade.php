<!-- resources/views/agendamentos/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Agendamentos</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('agendamentos.create') }}" class="btn btn-primary mb-3">Novo Agendamento</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Turno</th>
                    <th>Mês</th>
                    <th>Nome do Médico</th>
                    <th>Endereço do Consultório</th>
                    <th>Preço</th>
                    <th>Modalidade</th>
                    <th>Especialidade</th>
                    <th>CRM do Médico</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agendamentos as $agendamento)
                    <tr>
                        <td>{{ $agendamento->id }}</td>
                        <td>{{ $agendamento->turno }}</td>
                        <td>{{ $agendamento->mes }}</td>
                        <td>{{ $agendamento->nome_medico }}</td>
                        <td>{{ $agendamento->endereco_consultorio }}</td>
                        <td>{{ $agendamento->preco }}</td>
                        <td>{{ $agendamento->modalidade }}</td>
                        <td>{{ $agendamento->especialidade }}</td>
                        <td>{{ $agendamento->crm_medico }}</td>
                        <td>
                            <a href="{{ route('agendamentos.edit', $agendamento->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('agendamentos.destroy', $agendamento->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
