<?php

use Illuminate\Support\Facades\Route;
use Schrojf\Papers\Http\Controllers\PaperIndexController;
use Schrojf\Papers\Http\Controllers\PaperShowController;

Route::get('/', PaperIndexController::class)->name('papers.index');
