<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('app');
});

Route::resource('items', ItemController::class);

// Templates
Route::get('/templates/{template}', function($template){
    return view('templates.' . str_replace('.html', '', $template));
});
