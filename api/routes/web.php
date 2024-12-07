<?php

use Illuminate\Support\Facades\Route;

Route::head('/', function () {
    return view('welcome');
});
