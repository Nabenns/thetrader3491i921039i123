<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebhookController;
use App\Livewire\LandingPage;

Route::post('/midtrans/webhook', [WebhookController::class, 'handle'])->name('midtrans.webhook');

Route::get('/', LandingPage::class)->name('home');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/transactions', [\App\Http\Controllers\DashboardController::class, 'transactions'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.transactions');

Route::get('/langganan', [\App\Http\Controllers\SubscriptionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('subscription.index');

Route::get('/academy', [\App\Http\Controllers\AcademyController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('academy.index');

Route::get('/market-webinar', [\App\Http\Controllers\AcademyController::class, 'marketWebinar'])
    ->middleware(['auth', 'verified'])
    ->name('market-webinar.index');

Route::get('/academy/{video:slug}', [\App\Http\Controllers\AcademyController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('academy.show');

Route::get('/checkout/{package:slug}', \App\Livewire\Checkout::class)
    ->middleware(['auth', 'verified'])
    ->name('checkout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/invoice/{transaction}', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
});

require __DIR__.'/auth.php';
