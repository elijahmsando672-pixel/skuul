<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'apiLogin']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [LoginController::class, 'apiLogout']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/timetables', [AcademicController::class, 'timetables']);
    Route::get('/grades', [AcademicController::class, 'grades']);
    Route::get('/attendance', [AcademicController::class, 'attendance']);
    Route::get('/exams', [AcademicController::class, 'exams']);

    Route::get('/fee-structures', [FinanceController::class, 'feeStructures']);
    Route::get('/fee-records', [FinanceController::class, 'feeRecords']);
    Route::post('/fee-records', [FinanceController::class, 'storeFeeRecord']);
    Route::put('/fee-records/{feeRecord}', [FinanceController::class, 'updateFeeRecord']);

    Route::get('/books', [LibraryController::class, 'books']);
    Route::get('/book-issues', [LibraryController::class, 'issues']);
    Route::post('/book-issues', [LibraryController::class, 'issue']);
    Route::put('/book-issues/{bookIssue}/return', [LibraryController::class, 'returnIssue']);

    Route::get('/transport-routes', [TransportController::class, 'routes']);
    Route::get('/vehicles', [TransportController::class, 'vehicles']);
    Route::get('/transport-assignments', [TransportController::class, 'assignments']);

    $catalog = ['students', 'teachers', 'classrooms', 'subjects', 'academic-years', 'announcements'];

    Route::get('/{resource}', [CatalogController::class, 'index'])->whereIn('resource', $catalog);
    Route::post('/{resource}', [CatalogController::class, 'store'])->whereIn('resource', $catalog);
    Route::get('/{resource}/{id}', [CatalogController::class, 'show'])->whereIn('resource', $catalog);
    Route::put('/{resource}/{id}', [CatalogController::class, 'update'])->whereIn('resource', $catalog);
    Route::delete('/{resource}/{id}', [CatalogController::class, 'destroy'])->whereIn('resource', $catalog);
});
