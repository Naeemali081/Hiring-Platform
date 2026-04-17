<?php

use App\Http\Controllers\Api\CandidateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Candidate Portal SPA Routes (Public Token-Based Auth)
Route::get('/candidate/{token}', [CandidateController::class, 'show']);
Route::post('/candidate/{token}/start', [CandidateController::class, 'start']);
Route::post('/candidate/{token}/activity', [CandidateController::class, 'activity']);
Route::post('/candidate/{token}/submit', [CandidateController::class, 'submit']);
