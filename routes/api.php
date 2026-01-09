<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

Route::apiResource('tasks', TaskController::class);

Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
Route::delete('tasks/{id}/force', [TaskController::class, 'forceDelete']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
