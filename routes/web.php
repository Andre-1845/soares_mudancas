<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::post('/orcamento', [SiteController::class, 'storeLead'])->middleware('throttle:leads')->name('lead.store');
Route::post('/t', [SiteController::class, 'track'])->middleware('throttle:60,1')->name('track');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap']);
