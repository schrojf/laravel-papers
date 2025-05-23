<?php

use App\Http\Controllers\DummyPaperContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dummy_paper_content', DummyPaperContentController::class);
