<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\PackSizeCalculationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::resource('packs', PackController::class)->except(['index']);

Route::get('/calculatePacks', [PackSizeCalculationController::class, 'ReturnBestPackSizes'])->name('calculatePacks');

