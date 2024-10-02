<?php

use App\Http\Controllers\AgencyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerRecordController;
use App\Http\Controllers\OperatorsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PhonesNumberController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesTypeController;
use App\Http\Controllers\SalesUserController;
use App\Http\Controllers\TypificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [RegisterController::class, 'login'])->name('login');

// Protected Routes
Route::middleware('auth:api')->group(function () {
    // User Management
    Route::post('/logout', [RegisterController::class, 'logout'])->name('logout');
    Route::post('/change-password', [RegisterController::class, 'changePassword'])->name('change.password');
    Route::get('/user', [RegisterController::class, 'user'])->name('user');

    // Customers Records
    Route::resource('customers-records', CustomerRecordController::class)->except(['create', 'edit']);

    // Operators
    Route::resource('operators', OperatorsController::class)->except(['create', 'edit']);

    // Sales Types
    Route::resource('sales-type', SalesTypeController::class)->except(['create', 'edit']);

    // Typification
    Route::resource('typifications', TypificationController::class)->except(['create', 'edit']);

    // Agencies
    Route::resource('agencies', AgencyController::class)->except(['create', 'edit']);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

    // Phones
    Route::resource('phones', PhonesNumberController::class)->except(['create', 'edit']);

    // Sales User
    Route::resource('sales-user', SalesUserController::class)->except(['create', 'edit']);

    // Sales
    Route::resource('sales', SalesController::class)->except(['create', 'edit']);


});
