<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes publiques
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Routes pour la gestion des sites
    Route::resource('sites', SiteController::class);
    
    // Routes pour la gestion des transactions
    Route::resource('transactions', TransactionController::class);
    
    // Routes pour la gestion des devises
    Route::resource('currencies', CurrencyController::class);
    
    // Routes pour la gestion des types de transactions
    Route::resource('transaction-types', TransactionTypeController::class)->except(['show']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour les rapports PDF
    Route::get('/reports/transactions', [ReportController::class, 'transactionsReport'])->name('reports.transactions');
    Route::get('/reports/dashboard', [ReportController::class, 'dashboardReport'])->name('reports.dashboard');
});
