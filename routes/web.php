<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizManagerController;

use Illuminate\Support\Facades\Route;

Route::Get('/', fn () => to_route('login'));

require __DIR__ . '/auth.php';

Route::resource('/quiz', QuizController::class)->middleware('auth.weak');

Route::controller(QuizManagerController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/play', 'getQuestion')->name('playground');
        Route::post('/anwsers', 'postAnswer')->name('anwswer.store');
        Route::get('/results', 'getResults')->name('results.index');
    });
