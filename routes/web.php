<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('app');
});

// These must be BEFORE Route::resource to avoid {item} conflict
Route::get('/items/export/csv', [ItemController::class, 'exportCsv']);
Route::post('/items/upload-image', [ItemController::class, 'uploadImage']);
Route::post('/items/bulk-delete', [ItemController::class, 'bulkDelete']);
Route::post('/items/bulk-status', [ItemController::class, 'bulkStatus']);
Route::get('/items/suggestions', [ItemController::class, 'searchSuggestions']);

Route::resource('items', ItemController::class);
Route::resource('categories', CategoryController::class);

// Templates
Route::get('/templates/{template}', function ($template) {
    return view('templates.' . str_replace('.html', '', $template));
});
