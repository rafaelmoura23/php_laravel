@extends('layouts.app')

@section('content')
<div class="container my-4">

    @if (auth()->user()->tipo == 'medico')
        <h1>Bem-vindo(a), Dr(a) {{ auth()->user()->nome }}!</h1>
        <h2>Suas Consultas</h2>
        @if($consultas->isEmpty())
            <p>Nenhuma consulta encontrada.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Observações</th>
                        <th>Paciente</th>
                        <th>Ações</th> <!-- Coluna para ações -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultas as $consulta)
                        <tr>
                            <td>{{ $consulta->data }}</td>
                            <td>{{ $consulta->horario }}</td>
                            <td>{{ $consulta->observacoes }}</td>
                            <td>{{ $consulta->paciente->nome }}</td>
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
        @if($consultas->isEmpty())
            <p>Nenhuma consulta encontrada.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Observações</th>
                        <th>Médico(a)</th>
                        <th>Ações</th> <!-- Coluna para ações -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultas as $consulta)
                        <tr>
                            <td>{{ $consulta->data }}</td>
                            <td>{{ $consulta->horario }}</td>
                            <td>{{ $consulta->observacoes }}</td>
                            <td>{{ $consulta->medico->nome }}</td>
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
</div>
@endsection
