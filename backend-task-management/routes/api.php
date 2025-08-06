<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tasks', [\App\Http\Controllers\TaskController::class, 'index']);
Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store']);
Route::patch('/tasks/{task}/toogleStatus', [\App\Http\Controllers\TaskController::class, 'toggleStatus']);
Route::put('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update']);
Route::delete('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy']);

Route::get('/keywords', [\App\Http\Controllers\KeywordController::class, 'index']);
Route::post('/keywords', [\App\Http\Controllers\KeywordController::class, 'store']);
Route::put('/keywords/{keyword}', [\App\Http\Controllers\KeywordController::class, 'update']);
Route::delete('/keywords/{keyword}', [\App\Http\Controllers\KeywordController::class, 'destroy']);

