<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

// Welcome / name entry
Route::get('/',              [UserController::class, 'index'])->name('welcome');
Route::post('/start',        [UserController::class, 'store'])->name('user.store');
Route::post('/check-resume', [UserController::class, 'checkResume'])->name('user.checkResume');

// Quiz
Route::get('/quiz',     [QuizController::class, 'index'])->name('quiz.index');

// AJAX endpoints
Route::post('/quiz/next',   [QuizController::class, 'nextQuestion'])->name('quiz.next');
Route::post('/answer',      [AnswerController::class, 'store'])->name('answer.store');

// Result
Route::get('/result',   [ResultController::class, 'index'])->name('result.index');
