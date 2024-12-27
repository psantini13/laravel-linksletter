<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::controller(LinkController::class)->group(function(){
    Route::get('links', 'index')->name('links.index');
    Route::get('links/create', 'create')->name('links.create');
    Route::post('links', 'store')->name('links.store');
    Route::get('links/{link}/edit', 'edit')->name('links.edit');
    Route::put('links/{link}', 'update')->name('links.update');
    Route::delete('links/{link}', 'destroy')->name('links.destroy');

});

require __DIR__.'/auth.php';
