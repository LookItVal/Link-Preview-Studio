<?php

use App\Http\Controllers\PreviewController;
use Illuminate\Support\Facades\Route;

// This maps POST http://localhost/api/metadata to your controller.
Route::post('/metadata', PreviewController::class);