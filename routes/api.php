<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ColumnController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('boards', BoardController::class);
    Route::apiResource('boards.columns', ColumnController::class)->shallow();
    Route::apiResource('columns.tasks', TaskController::class)->shallow();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});