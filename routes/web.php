<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizManagerController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::Get('/', fn () => to_route('login'));

require __DIR__ . '/auth.php';

Route::resource('/quizzes', QuizController::class)->except('show', 'edit')->middleware('auth.weak');

Route::get('/play', [QuizManagerController::class, 'getQuestion'])->name('playground')->middleware('auth');

Route::post('/anwsers', [AnswerController::class, 'recordChoice'])->name('anwswer.store')->middleware('auth');

Route::controller(RankingController::class)
    ->middleware('auth')
    ->prefix('results')
    ->group(function () {
        Route::get('/current_quiz', 'currentQuizResults')->name('ranking.current_quiz');
        Route::get('/global', 'globalResults')->name('ranking.global');
    });
