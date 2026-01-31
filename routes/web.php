<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebhookController;
use App\Livewire\LandingPage;

Route::post('/midtrans/webhook', [WebhookController::class, 'handle'])->name('midtrans.webhook');

Route::get('/', LandingPage::class)->name('home');
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/transactions', [\App\Http\Controllers\DashboardController::class, 'transactions'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.transactions');

Route::get('/dashboard/record', [\App\Http\Controllers\DashboardController::class, 'record'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.record');

Route::get('/langganan', [\App\Http\Controllers\SubscriptionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('subscription.index');

Route::get('/academy', [\App\Http\Controllers\AcademyController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('academy.index');

Route::get('/market-webinar', [\App\Http\Controllers\AcademyController::class, 'marketWebinar'])
    ->middleware(['auth', 'verified'])
    ->name('market-webinar.index');

Route::post('/market-webinar/topic', [\App\Http\Controllers\AcademyController::class, 'submitTopic'])
    ->middleware(['auth', 'verified'])
    ->name('market-webinar.topic.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/journal', [\App\Http\Controllers\JournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/export', [\App\Http\Controllers\JournalController::class, 'export'])->name('journal.export');
    Route::get('/journal/template', [\App\Http\Controllers\JournalController::class, 'downloadTemplate'])->name('journal.template');
    Route::post('/journal/import', [\App\Http\Controllers\JournalController::class, 'import'])->name('journal.import');
    Route::get('/journal/create', [\App\Http\Controllers\JournalController::class, 'create'])->name('journal.create');
    Route::post('/journal', [\App\Http\Controllers\JournalController::class, 'store'])->name('journal.store');
    Route::get('/journal/{journal}', [\App\Http\Controllers\JournalController::class, 'show'])->name('journal.show');
    Route::get('/journal/{journal}/edit', [\App\Http\Controllers\JournalController::class, 'edit'])->name('journal.edit');
    Route::put('/journal/{journal}', [\App\Http\Controllers\JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{journal}', [\App\Http\Controllers\JournalController::class, 'destroy'])->name('journal.destroy');
    Route::post('/journal/goal', [\App\Http\Controllers\JournalController::class, 'setGoal'])->name('journal.goal');
});

Route::get('/academy/{video:slug}', [\App\Http\Controllers\AcademyController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('academy.show');

Route::post('/academy/{video}/watchlist', [\App\Http\Controllers\AcademyController::class, 'toggleWatchlist'])
    ->middleware(['auth', 'verified'])
    ->name('academy.watchlist.toggle');

Route::post('/academy/{video}/complete', [\App\Http\Controllers\AcademyController::class, 'markAsComplete'])
    ->middleware(['auth', 'verified'])
    ->name('academy.complete');

Route::post('/academy/{video}/note', [\App\Http\Controllers\AcademyController::class, 'saveNote'])
    ->middleware(['auth', 'verified'])
    ->name('academy.note.save');

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
