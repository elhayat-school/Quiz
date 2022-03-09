<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Question;


Route::post('/questions', [QuestionController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);



Route::group(['middleware' => ['auth:sanctum']], function () {

     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/questions', [QuizManagerController::class, 'getQuestion']);

});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

