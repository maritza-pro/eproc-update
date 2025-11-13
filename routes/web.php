<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', fn () => redirect()->route('filament.dashboard.pages.dashboard'));
Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/inf2', function () {
    return view('vendor');
});
// Route::get('/dashboard/orders/{order}/print', [\App\Actions\PrintOrder::class, 'handle'])
//     ->name('order.print')
//     ->middleware(['auth', 'verified'])
//     ->where('order', '[0-9]+');
