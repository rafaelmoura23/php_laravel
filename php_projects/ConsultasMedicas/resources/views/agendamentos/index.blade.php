@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Agendamentos</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <a href="{{ route('agendamentos.create') }}" class="btn btn-primary mb-3">
        Novo Agendamento
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Turno</th>
                    <th>Mês</th>
                    <th>Nome do Médico</th>
                    <th>Endereço</th>
                    <th>Preço</th>
                    <th>Modalidade</th>
                    <th>Especialidade</th>
                    <th>CRM</th>
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
                        <td class="text-center">
                            <a href="{{ route('agendamentos.edit', $agendamento->id) }}" class="btn btn-sm btn-warning">
                                Editar
                            </a>
                            <form action="{{ route('agendamentos.destroy', $agendamento->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
