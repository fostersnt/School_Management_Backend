<?php

use App\Http\Controllers\FileUploadController;
use App\Jobs\UserMailJob;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function(){
    $user = User::query()->first();

    return json_encode($user);
    // return view('home');
});

Route::get('/abcd', function () {
    UserMailJob::dispatch('Asante', 'fostersnt@gmail.com');
    return 'success';
    return view('user_mail');
});

Route::prefix('admin')->group(function(){
    Route::get('/', [FileUploadController::class,'index'])->name('admin.index');
    Route::post('file/upload', [FileUploadController::class, 'fileUpload'])->name('file.upload');
});
