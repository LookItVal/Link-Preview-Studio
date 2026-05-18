<?php

use App\Http\Controllers\PreviewController;
use Illuminate\Support\Facades\Route;

// This maps GET http://localhost/api/metadata to your controller.
Route::get('/metadata', PreviewController::class);