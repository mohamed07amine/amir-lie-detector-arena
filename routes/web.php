<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\VoteController;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
Route::post('/games/{game}/start', [GameController::class, 'start'])->name('games.start');
Route::post('/join', [GameController::class, 'join'])->name('games.join');

// Hna fin derti l-form f Part 3
Route::post('/statements', [StatementController::class, 'store'])->name('statements.store');
Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
Route::get('/games/{game}/results', [GameController::class, 'results'])->name('games.results');

