<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="bi bi-heart-pulse-fill"></i> CONSULT.IA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <!-- Adicione mais itens de navegação conforme necessário -->
            </ul>
            <div class="d-flex align-items-center">
                @if (Auth::check())
                    <div class="d-flex align-items-center">
                        @if (Auth::user()->isMedico())
                            <a href="/agendamentos" class="btn btn-primary me-2">
                                <i class="bi bi-calendar-plus"></i> Agendamento
                            </a>
                            <a href="/dashboard" class="btn btn-secondary me-2">
                                <i class="bi bi-journal-check"></i> Consultas
                            </a>
                        @endif
                        <span class="me-3">Bem-vindo, <strong>{{ Auth::user()->nome }}</strong></span>
                        @if (Auth::user()->isUsuario())
                            <a href="/medicos" class="btn btn-primary me-2">
                                <i class="bi bi-person-circle"></i> Médicos
                            </a>
                            <a href="/dashboard" class="btn btn-secondary me-2">
                                <i class="bi bi-journal-medical"></i> Consultas
                            </a>
                        @endif
                        <form action="/logout" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="btn btn-outline-primary me-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="/registro" class="btn btn-warning">
                        <i class="bi bi-person-plus"></i> Sign-up
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
