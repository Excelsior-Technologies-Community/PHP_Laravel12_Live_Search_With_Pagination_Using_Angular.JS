<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('app');
});

Route::resource('items', ItemController::class);
Route::get('/items', [ItemController::class, 'index']);

// Templates
Route::get('/templates/{template}', function ($template) {
    return view('templates.' . str_replace('.html', '', $template));
});

Route::get('/items/export/csv', [ItemController::class, 'exportCsv']);
