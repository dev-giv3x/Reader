<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StatusController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('auth.local')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/my-books', [BookController::class, 'myBooks']);
    Route::post('/books/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/books/{id}/rating', [RatingController::class, 'store']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
    Route::post('/books/{id}/status', [StatusController::class, 'store']);
    Route::delete('/books/{id}/status', [StatusController::class, 'destroy']);
    Route::get('/statuses/{status}', [StatusController::class, 'index']);
    Route::get('/books/{id}/comments', [CommentController::class, 'index']);
});

Route::get('/books/search',[BookController::class, 'search']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books', [BookController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);




