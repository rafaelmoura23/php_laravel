<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/produtos', function () {
    return view('produtos');
});

Route::get('/contatos', function () {
    return view('contatos');
});