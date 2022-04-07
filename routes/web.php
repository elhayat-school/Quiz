<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizManagerController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::Get('/', fn () => to_route('login'));

require __DIR__ . '/auth.php';

Route::resource('/quizzes', QuizController::class)->except('show', 'edit')->middleware('auth.weak');

Route::controller(QuizManagerController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/play', 'getQuestion')->name('playground');
        Route::post('/anwsers', 'postAnswer')->name('anwswer.store');
    });

Route::controller(RankingController::class)
    ->middleware('auth')
    ->prefix('results')
    ->group(function () {
        Route::get('/current_quiz', 'currentQuizResults')->name('ranking.current_quiz');
        Route::get('/global', 'globalResults')->name('ranking.global');
    });
