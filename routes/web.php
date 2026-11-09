<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// The home page just sends visitors to the report.
Route::redirect('/', '/report');

Route::get('/report', [ReportController::class, 'index'])->name('report');

Route::get('/events', [EventController::class, 'index'])->name('events.index');