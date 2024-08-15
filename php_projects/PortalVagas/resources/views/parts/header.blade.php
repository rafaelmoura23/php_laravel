@if (Auth::check())
    @if (Auth::user()->isEmpresa())
        <div>
            <a href="/vagas">Acesse o Dashboard de Vagas</a>
        </div>
    @endif
    <hr>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h3>Seja bem-vindo, {{ Auth::user()->nome }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
    <hr>
@else
    <hr>
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-auto">
                <a href="/login" class="btn btn-outline me-2">Login</a>
                <a href="/registro" class="btn btn-warning">Sign-up</a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-auto">
                <ul class="nav">
                    <li class="nav-item"><a href="/" class="nav-link text-secondary">Home</a></li>
                </ul>
            </div>
        </div>
    </div>
    <hr>
@endif
