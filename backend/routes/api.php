<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/images', [ImageController::class, 'AllImages']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/image/{url}', [ImageController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/myimages', [ImageController::class, 'index']);
    Route::delete('images/{id}',[ImageController::class,'destroy']);
    Route::put('images/{id}',[ImageController::class,'update']);
    Route::post('/upload',[ImageController::class,'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/user/delete', [AuthController::class, 'delete']);
});