<?php

use App\Http\Controllers\UserController;
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

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index'); // '/api/users'
    Route::post('/store', [UserController::class, 'store'])->name('user.store'); // '/api/users/store'
    Route::get('/nickname/{nickname}', [UserController::class, 'showByNickname'])->name('user.showByNickname'); // api/users/nickname/{nickname}
    Route::get('{user}', [UserController::class, 'show'])->name('user.show'); // '/api/users/{id}'
    Route::put('{user}', [UserController::class, 'update'])->name('user.update'); // '/api/users/{id}'
});
