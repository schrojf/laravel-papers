<?php

use Illuminate\Support\Facades\Route;
use Schrojf\Papers\Http\Controllers\PaperActionController;
use Schrojf\Papers\Http\Controllers\PaperIndexController;
use Schrojf\Papers\Http\Controllers\PaperShowController;

Route::get('/', PaperIndexController::class)->name('papers.index');
Route::get('/{paper}/{page?}', PaperShowController::class)->name('papers.show');
Route::post('/{paper}/{action?}', PaperActionController::class)->name('papers.action');
