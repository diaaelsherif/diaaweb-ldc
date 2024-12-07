<?php

use Illuminate\Support\Facades\Route;

Route::match('HEAD', '/', function () {
    return null;
});
