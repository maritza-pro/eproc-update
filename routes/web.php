<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('filament.dashboard.pages.dashboard'));

// Route::get('/dashboard/orders/{order}/print', [\App\Actions\PrintOrder::class, 'handle'])
//     ->name('order.print')
//     ->middleware(['auth', 'verified'])
//     ->where('order', '[0-9]+');
