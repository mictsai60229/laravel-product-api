<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Elasticsearch\IndicesController;
use App\Http\Controllers\Elasticsearch\CatController;
use App\Http\Controllers\Elasticsearch\DocumentController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// indices
Route::put('/indices/create', [IndicesController::class, 'create']);
Route::delete('/indices/delete', [IndicesController::class, 'delete']);
Route::post('/indices/bulk', [IndicesController::class, 'bulk']);
Route::post('/indices/refresh', [IndicesController::class, 'refresh']);
Route::post('/indices/putSettings', [IndicesController::class, 'putSettings']);
Route::post('/indices/setInterval', [IndicesController::class, 'setInterval']);
Route::post('/indices/updateAliases', [IndicesController::class, 'updateAliases']);
Route::post('/indices/{action}Aliases', [IndicesController::class, 'addOrRemoveAliases']);

//Document
Route::match(['get', 'post'], '/document/search', [DocumentController::class, 'search']);


//cat
Route::get('/cat/indices', [CatController::class, 'indices']);
Route::get('/cat/aliases', [CatController::class, 'aliases']);

