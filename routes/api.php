<?php

use App\Http\Controllers\FileStorage\DiskController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('storage')->group(function () {
    Route::get('/search', [DiskController::class, 'search'])->name('search');
    Route::post('/upload', [DiskController::class, 'upload'])->name('upload');
    Route::post('/download', [DiskController::class, 'download'])->name('download');
    Route::post('/create/directory', [DiskController::class, 'createDirectory'])->name('create-directory');
    Route::patch('/rename', [DiskController::class, 'rename'])->name('rename-file');
});

require __DIR__.'/auth.php';
