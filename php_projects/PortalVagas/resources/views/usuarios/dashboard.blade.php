<div class="container">
    <h3>Dashboard - Usuário</h3>
    <form action="{{route('usuarios.logout')}}" method="post">
        @csrf
        <input type="submit" value="sair">
    </form>

    @if(Auth::check())
        <span>voce esta logado</span>        
    @endif
</div>