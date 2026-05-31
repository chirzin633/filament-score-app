<?php

use App\Http\Controllers\RaportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::get('/raport/{student}/download', [RaportController::class, 'download'])->name('raport.download')->middleware('auth');