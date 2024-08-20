==== Passo a Passo - Projeto: ====
Criação do projeto: 
- composer create laravel/laravel ConsultasMedicas --prefer-dist

Criação banco de dados:
- Create database consultas_medicas

Edição .env:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=consultas_medicas
DB_USERNAME=postgres
DB_PASSWORD=postgres

Criação dos models:
- php artisan make:model Usuario -m
- php artisan make:model Consulta -m
- php artisan make:model Agendamento -m
rodar: php artisan migrate(realizar alterações/criações das tabelas no banco de dado)

Editar os arquivos do banco de dados
Rodar php artisam migrate

Criação do Controller para Usuario:
- php artisan make:controller UsuarioController

Definir as rotas em routes/web.php

Criar o layouts.app
- php artisan make:view layouts.app

Criar Header e Footer:
- php artisan make:view parts.header
- php artisan make:view parts.footer


Criar View:
- php artisan make:view usuarios.registro
- php artisan make:view usuarios.login
- php artisan make:view usuarios.dashboard

Criar a Home:
- php artisan make:view home

Criar HomeController
- php artisan make:controller HomeController

Criar DashboardController
- php artisan make:controller DashboardController