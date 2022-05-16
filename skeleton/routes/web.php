<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->get('/{any}', function () {
    return view('name::home');
})->where('any', '^(?!api|css|js|fonts|public|storage).*');
