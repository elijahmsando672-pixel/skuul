<?php

use App\Http\Controllers\Api\MpesaCallbackController;
use App\Http\Controllers\Platform\DashboardController as PlatformDashboard;
use App\Http\Controllers\Platform\PlanController as PlatformPlanController;
use App\Http\Controllers\Platform\SchoolController as PlatformSchoolController;
use App\Http\Controllers\Public\SchoolRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = \App\Models\Plan::where('is_active', true)->orderBy('monthly_price')->get();
    return view('marketing.home', compact('plans'));
})->name('home');

Route::get('/register-school', [SchoolRegistrationController::class, 'create'])->name('school.register');
Route::post('/register-school', [SchoolRegistrationController::class, 'store'])->name('school.register.store');

Route::post('/api/payments/mpesa/callback', MpesaCallbackController::class)->name('payments.mpesa.callback');

Route::middleware(['auth','platform'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/dashboard', [PlatformDashboard::class, 'index'])->name('dashboard');
    Route::get('/schools', [PlatformSchoolController::class, 'index'])->name('schools.index');
    Route::get('/schools/{school}', [PlatformSchoolController::class, 'show'])->name('schools.show');
    Route::post('/schools/{school}/approve', [PlatformSchoolController::class, 'approve'])->name('schools.approve');
    Route::post('/schools/{school}/suspend', [PlatformSchoolController::class, 'suspend'])->name('schools.suspend');
    Route::post('/schools/{school}/reactivate', [PlatformSchoolController::class, 'reactivate'])->name('schools.reactivate');
    Route::get('/plans', [PlatformPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [PlatformPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [PlatformPlanController::class, 'update'])->name('plans.update');
});
