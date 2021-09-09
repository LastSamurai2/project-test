<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('algorithmInline', 'App\Http\Controllers\Bizami\Algorithm\Inline');
Route::apiResource('SalesWidget', 'App\Http\Controllers\Bizami\Dashboard\SalesWidget');
Route::apiResource('LastImportsWidget', 'App\Http\Controllers\Bizami\Dashboard\Widget\LastImports');
Route::apiResource('FlowErrorsWidget', 'App\Http\Controllers\Bizami\Dashboard\Widget\FlowErrors');

