@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4 text-primary">Login</h2>
        <form method="POST" action="{{ route('usuarios.login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="font-weight-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Digite seu email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="font-weight-bold">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="Digite sua senha" required>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>

            <div class="text-center mt-3">
                <a href="" class="text-muted">Esqueceu sua senha?</a>
            </div>
        </form>
    </div>
</div>
@endsection
