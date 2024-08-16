@extends('layouts.app')

@section('content')
@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Dashboard de Vagas</h1>

    <!-- Formulário de Pesquisa -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4 d-flex">
        <input type="search" name="search" class="form-control me-2" placeholder="Pesquisar vagas..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>

    <!-- Cards de Vagas -->
    <div class="row">
        @foreach ($vagas as $vaga)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="{{ asset('assets/img/img1.png') }}" class="card-img-top" alt="{{ $vaga->titulo }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $vaga->titulo }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $vaga->empresa }}</h6>
                        <p class="card-text">{{ $vaga->descricao }}</p>
                        <p class="card-text"><strong>Local:</strong> {{ $vaga->localizacao }}</p>
                        <p class="card-text"><strong>Salário:</strong> R$ {{ number_format($vaga->salario, 2, ',', '.') }}</p>
                        <a href="{{ route('vagas.show', $vaga->id) }}" class="btn btn-primary">Ver Vaga</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
