<?php
use Illuminate\Support\Facades\Route;
use Base\Controllers\MakeController;

Route::post('/client-config', [MakeController::class, 'clientConfig']);

Route::get('/', function () {return view('layouts.home');});