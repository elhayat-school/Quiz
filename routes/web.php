<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\PlaygroundController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::Get('/', fn () => to_route('login'));

require __DIR__ . '/auth.php';

Route::resource('/quizzes', QuizController::class)->except('show', 'edit')->middleware('is_admin');

Route::prefix('play')
    ->middleware('auth')
    ->group(function () {
        Route::get('', [PlaygroundController::class, 'getQuizContext'])->name('playground');
        Route::post('answer', [AnswerController::class, 'recordChoice'])->name('answer');
    });

Route::controller(RankingController::class)
    ->prefix('results')
    ->middleware('auth')
    ->group(function () {
        Route::get('/current_quiz', 'currentQuizResults')->name('ranking.current_quiz');
        Route::get('/global', 'globalResults')->name('ranking.global');
    });
