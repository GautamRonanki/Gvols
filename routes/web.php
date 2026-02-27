<?php

use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RfiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{slug}', [ProgramController::class, 'show'])->name('programs.show');
Route::post('/rfi', [RfiController::class, 'store'])->name('rfi.store');
