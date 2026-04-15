<?php

use App\Http\Controllers\Api\EvaluationApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/student/assignments', [EvaluationApiController::class, 'assignments']);

    Route::get('/questions', [EvaluationApiController::class, 'questions']);

    Route::post('/submit-evaluation', [EvaluationApiController::class, 'submit']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});