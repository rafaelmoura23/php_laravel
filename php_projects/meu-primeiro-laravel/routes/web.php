<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProdutoController;

Route::get('/', function () {
    return view('home');
});

Route::get('/produtos', function () {
    return view('produtos');
});

Route::get('/produtos', [ProdutoController::class, 'index']);

Route::get('/contatos', function () {
    return view('contatos');
});


use App\Http\Controllers\CssController;

Route::get('css/{filename}', [CssController::class, 'getCss']);

