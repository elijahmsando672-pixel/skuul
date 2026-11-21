<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\FeeController as AdminFeeController;
use App\Http\Controllers\Admin\ImpersonationController as AdminImpersonationController;
use App\Http\Controllers\Admin\LibraryController as AdminLibraryController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\TimetableController as AdminTimetableController;
use App\Http\Controllers\Admin\TransportController as AdminTransportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Parent\FeeController as ParentFeeController;
use App\Http\Controllers\Parent\TransportController as ParentTransportController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\LibraryController as StudentLibraryController;
use App\Http\Controllers\Student\TransportController as StudentTransportController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\LibraryController as TeacherLibraryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public home moved to routes/saas.php

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password reset
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetPasswordEmail'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

// Dashboard (requires auth)
Route::middleware(['auth','subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/admin/impersonate/leave', [AdminImpersonationController::class, 'destroy'])->name('admin.impersonate.leave');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::post('/impersonate/{user}', [AdminImpersonationController::class, 'store'])->name('impersonate')->whereNumber('user');
        Route::resource('/users', AdminUserController::class);
        Route::resource('/students', AdminStudentController::class);
        Route::resource('/teachers', AdminTeacherController::class);
        Route::resource('/classrooms', ClassroomController::class);
        Route::resource('/subjects', SubjectController::class);
        Route::resource('/academic-years', AcademicYearController::class)->parameters(['academic-years' => 'year', '/academic-years' => 'year']);
        Route::resource('/timetables', AdminTimetableController::class);
        Route::get('/fees/records', [AdminFeeController::class, 'records'])->name('fees.records');
        Route::post('/fees/records', [AdminFeeController::class, 'recordStore'])->name('fees.records.store');
        Route::post('/fees/records/{feeRecord}/status', [AdminFeeController::class, 'recordStatus'])->name('fees.records.status');
        Route::resource('/fees', AdminFeeController::class);
        Route::get('/exams/{exam}/results', [AdminExamController::class, 'results'])->name('exams.results');
        Route::post('/exams/{exam}/results', [AdminExamController::class, 'resultsStore'])->name('exams.results.store');
        Route::post('/exams/{exam}/publish', [AdminExamController::class, 'publish'])->name('exams.publish');
        Route::resource('/exams', AdminExamController::class);
        Route::get('/library/issues', [AdminLibraryController::class, 'issues'])->name('library.issues');
        Route::post('/library/issues', [AdminLibraryController::class, 'issue'])->name('library.issues.store');
        Route::put('/library/issues/{bookIssue}/return', [AdminLibraryController::class, 'returnBook'])->name('library.issues.return');
        Route::resource('/library', AdminLibraryController::class)->parameters(['library' => 'book'])->names('library');
        Route::prefix('/transport')->name('transport.')->group(function () {
            Route::resource('/routes', AdminTransportController::class)
                ->parameters(['routes' => 'route'])
                ->names('routes');
            Route::get('/vehicles', [AdminTransportController::class, 'vehicles'])->name('vehicles');
            Route::post('/vehicles', [AdminTransportController::class, 'vehicleStore'])->name('vehicles.store');
            Route::put('/vehicles/{vehicle}', [AdminTransportController::class, 'vehicleUpdate'])->name('vehicles.update');
            Route::delete('/vehicles/{vehicle}', [AdminTransportController::class, 'vehicleDestroy'])->name('vehicles.destroy');
            Route::get('/assignments', [AdminTransportController::class, 'assignments'])->name('assignments');
            Route::post('/assignments', [AdminTransportController::class, 'assignmentStore'])->name('assignments.store');
            Route::delete('/assignments/{assignment}', [AdminTransportController::class, 'assignmentDestroy'])->name('assignments.destroy');
        });
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [AdminNotificationController::class, 'send'])->name('notifications.send');
        Route::resource('/announcements', AnnouncementController::class);
    });

    // Teacher routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
        Route::get('/timetable', [AdminTimetableController::class, 'teacher'])->name('timetable.index');
        Route::get('/attendance', [AttendanceController::class, 'teacherIndex'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/get-students', [AttendanceController::class, 'getStudents'])->name('attendance.get-students');
        Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index');
        Route::get('/library', [TeacherLibraryController::class, 'index'])->name('library.index');
        Route::resource('/grades', GradeController::class);
    });

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/timetable', [AdminTimetableController::class, 'student'])->name('timetable.index');
        Route::get('/attendance', [AttendanceController::class, 'studentIndex'])->name('attendance.index');
        Route::get('/grades', [GradeController::class, 'studentIndex'])->name('grades.index');
        Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('/library', [StudentLibraryController::class, 'index'])->name('library.index');
        Route::get('/transport', [StudentTransportController::class, 'index'])->name('transport.index');
    });

    // Parent routes
    Route::middleware(['role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentDashboard::class, 'index'])->name('dashboard');
        Route::get('/fees', [ParentFeeController::class, 'index'])->name('fees.index');
        Route::get('/transport', [ParentTransportController::class, 'index'])->name('transport.index');
        Route::get('/children/{student}/attendance', [AttendanceController::class, 'parentIndex'])->name('attendance.index');
        Route::get('/children/{student}/grades', [GradeController::class, 'parentIndex'])->name('grades.index');
    });
});
