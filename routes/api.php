<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizManagerController;

use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::controller(QuizManagerController::class)
    ->middleware((['auth:sanctum']))
    ->group(function () {
        Route::get('/questions', 'getQuestion');
        Route::get('/start', 'getStartAt');
    });

Route::post('/questions', [QuestionController::class, 'store']);
