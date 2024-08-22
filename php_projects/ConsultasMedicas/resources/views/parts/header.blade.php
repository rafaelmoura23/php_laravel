<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">SeuLogo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
            </ul>
            <div class="d-flex">
                @if (Auth::check())
                    <div class="d-flex align-items-center">
                        @if (Auth::user()->isMedico())
                            <a href="/agendamentos" class="btn btn-primary me-2">Agendamento</a>
                            <a href="/agendamentos" class="btn btn-secondary me-2">Consultas Marcadas</a>
                        @endif
                        @if (Auth::user()->isUsuario())
                            <a href="/medicos" class="btn btn-primary me-2">Médicos</a>
                        @endif
                        <span class="me-3">Bem-vindo, {{ Auth::user()->nome }}</span>
                        <form action="/logout" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                    <a href="/registro" class="btn btn-warning">Sign-up</a>
                @endif
            </div>
        </div>
    </div>
</nav>