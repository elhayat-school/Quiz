<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizManagerController;
use Illuminate\Support\Facades\Route;


Route::post('/questions', [QuestionController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);



Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/questions', [QuizManagerController::class, 'getQuestion']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/questions', [QuizManagerController::class, 'getQuestion']);
});
