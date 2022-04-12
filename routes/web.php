<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\PlaygroundController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::Get('/', fn () => to_route('login'));

require __DIR__ . '/auth.php';

Route::middleware('auth', 'is_admin')->group(function () {

    Route::resource('quizzes', QuizController::class)->except('show', 'edit', 'update');

    Route::controller(QuizController::class)
        ->prefix('quizzes/{quiz}')
        ->group(function () {
            Route::match(['put', 'patch'], 'done', 'markAsDone')->name('quizzes.done_state');
            Route::match(['put', 'patch'], 'participation-stats', 'cacheParticipationStats')->name('quizzes.cache_participation_stats');
        });
});

Route::middleware('auth')->group(function () {

    Route::prefix('play')
        ->group(function () {
            Route::get('', [PlaygroundController::class, 'getQuizContext'])->name('playground');
            Route::post('answer', [AnswerController::class, 'recordChoice'])->name('answer');
        });

    Route::controller(RankingController::class)
        ->prefix('results')
        ->group(function () {
            Route::get('/current_quiz', 'currentQuizResults')->name('ranking.current_quiz');
            Route::get('/global', 'globalResults')->name('ranking.global')
                ->middleware('is_admin'); // REMOVE LATER
        });
});
