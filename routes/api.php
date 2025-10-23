<?php

use App\Http\Controllers\Tenants\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TenantIdentification;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(TenantController::class)->prefix('tenants')->group(function(){
    Route::get('/', 'index');
    Route::post('/create', 'create')->withoutMiddleware(TenantIdentification::class);
});

Route::controller(UserController::class)
->prefix('users')
->group(function(){
    Route::post('/create', 'create');
});