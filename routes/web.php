<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,ChatController};

Route::get('/', function () {
    return view('welcome');
});
Route::get('/chat', [ChatController::class, 'chatpage']);
Route::post('/chat', [ChatController::class, 'chat']);