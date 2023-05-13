<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerDataController;
use App\Http\Controllers\Api\EmployeeDataController;
use App\Http\Controllers\Api\ExpenditureController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\OwnerDataController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PackageDataController;
use App\Http\Controllers\Api\RecapitulationController;
use App\Http\Controllers\Api\StuffController;
use App\Http\Controllers\Api\StuffDataController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 
// Authentication
// 
Route::post('/sign-up', [AuthenticationController::class, 'signup']);
Route::post('/sign-in', [AuthenticationController::class, 'signin']);
// 
// Notification Midtrans
// 
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function () {
    // 
    // Customer Data
    // 
    Route::get('/customer-data', [CustomerDataController::class, 'fetch']);
    Route::post('/customer-data', [CustomerDataController::class, 'post']);
    Route::put('/customer-data', [CustomerDataController::class, 'put']);
    Route::delete('/customer-data', [CustomerDataController::class, 'delete']);
    
    // 
    // Packages
    // 
    Route::get('/customers', [CustomerController::class, 'fetch']);
    
    // 
    // Packages
    // 
    Route::get('/packages', [PackageController::class, 'fetch']);
    
    // 
    // Package Data
    // 
    Route::get('/package-data', [PackageDataController::class, 'fetch']);
    Route::post('/package-data', [PackageDataController::class, 'post']);
    Route::put('/package-data', [PackageDataController::class, 'put']);
    Route::delete('/package-data', [PackageDataController::class, 'delete']);

    // 
    // Stuffs
    // 
    Route::get('/stuffs', [StuffController::class, 'fetch']);

    // 
    // Stuff Data
    // 
    Route::get('/stuff-data', [StuffDataController::class, 'fetch']);
    Route::post('/stuff-data', [StuffDataController::class, 'post']);
    Route::put('/stuff-data', [StuffDataController::class, 'put']);
    Route::delete('/stuff-data', [StuffDataController::class, 'delete']);
    
    // 
    // Update Profile
    // 
    Route::put('/update-profile', [AuthenticationController::class, 'updateprofile']);
    
    // 
    // Update Account
    // 
    Route::put('/update-account', [AuthenticationController::class, 'updateaccount']);

    // 
    // Authentication
    // 
    Route::post('/sign-out', [AuthenticationController::class, 'signout']);

    // 
    // Transactions
    // 
    Route::get('/transactions', [TransactionController::class, 'fetch']);
    Route::post('/transactions', [TransactionController::class, 'post']);
    Route::put('/transactions', [TransactionController::class, 'putStatusToReadyToBeTaken']);
    Route::put('/checkout-cash', [TransactionController::class, 'putCheckoutCash']);
    Route::put('/checkout-cashless', [TransactionController::class, 'putCheckoutCashless']);
    Route::get('/check-status', [TransactionController::class, 'checkStatus']);
    Route::get('/download-transactions', [TransactionController::class, 'download']);
    Route::delete('/transactions', [TransactionController::class, 'delete']);

    // 
    // Expenditures
    // 
    Route::get('/expenditures', [ExpenditureController::class, 'fetch']);
    Route::post('/expenditures', [ExpenditureController::class, 'post']);
    Route::delete('/expenditures', [ExpenditureController::class, 'delete']);

    // 
    // Owner Users
    // 
    Route::get('/owner-data', [OwnerDataController::class, 'fetch']);
    Route::post('/owner-data', [OwnerDataController::class, 'post']);
    Route::put('/owner-data', [OwnerDataController::class, 'put']);
    Route::delete('/owner-data', [OwnerDataController::class, 'delete']);
    
    // 
    // Owner Users
    // 
    Route::get('/employee-data', [EmployeeDataController::class, 'fetch']);
    Route::post('/employee-data', [EmployeeDataController::class, 'post']);
    Route::put('/employee-data', [EmployeeDataController::class, 'put']);
    Route::delete('/employee-data', [EmployeeDataController::class, 'delete']);

    // 
    // Recapitulation
    // 
    Route::get('/recapitulations', [RecapitulationController::class, 'fetch']);
   
    
});


