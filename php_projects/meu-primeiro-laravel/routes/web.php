<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello World!!!';
});

Route::get('/HelloWorld', function () {
    return view('HelloWorld');
});
