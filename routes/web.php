<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizManagerController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

require __DIR__ . '/auth.php';

Route::resource('/quiz', QuizController::class)->middleware('auth.weak');

Route::controller(QuizManagerController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/play', 'getQuestion')->name('playground');
        Route::post('/anwsers', 'postAnswer')->name('anwswer.store');
        Route::get('/results', 'getResults')->name('relults.index');
    });
