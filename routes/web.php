<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BusinessController as WebBusinessController;
use App\Http\Controllers\Web\AppointmentController as WebAppointmentController;
use App\Http\Controllers\Web\ServiceController as WebServiceController;
use App\Http\Controllers\Web\EmployeeController as WebEmployeeController;
use App\Http\Controllers\Web\ClientController as WebClientController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ReportController;
use App\Models\Business;
use Illuminate\Support\Facades\Route;

// ============================================
// ПУБЛИЧНЫЕ МАРШРУТЫ
// ============================================
Route::get('/', function () {
    $businesses = Business::withCount(['clients', 'appointments'])
        ->where('status', 'active')
        ->latest()
        ->limit(8)
        ->get();

    return view('welcome', compact('businesses'));
})->name('home');

// Публичные страницы компаний
Route::get('/companies', [App\Http\Controllers\Public\CompanyController::class, 'index'])->name('public.companies');
Route::get('/company/{businessSlug}', [App\Http\Controllers\Public\CompanyController::class, 'show'])->name('public.company');
Route::get('/booking/{businessSlug}', [App\Http\Controllers\Public\BookingController::class, 'index'])->name('public.booking');
Route::post('/booking/{businessSlug}', [App\Http\Controllers\Public\BookingController::class, 'store'])->name('public.booking.store');

// ============================================
// РЕГИСТРАЦИЯ С SMS (ПУБЛИЧНЫЕ МАРШРУТЫ)
// ============================================
Route::post('/register/phone', [App\Http\Controllers\Auth\RegisteredUserController::class, 'sendPhoneCode'])
    ->name('register.phone');

Route::post('/register/verify', [App\Http\Controllers\Auth\RegisteredUserController::class, 'verifyPhone'])
    ->name('register.verify');

// Auth маршруты
require __DIR__.'/auth.php';

// ============================================
// ПРОФИЛЬ - ДОСТУПЕН ВСЕМ АВТОРИЗОВАННЫМ
// ============================================
Route::middleware(['auth', 'verified'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');

    Route::post('/phone/send-code', [ProfileController::class, 'sendVerificationCode'])->name('phone.send-code');
    Route::post('/phone/verify', [ProfileController::class, 'verifyPhone'])->name('phone.verify');
});

// ============================================
// КЛИЕНТСКАЯ ЧАСТЬ (ТОЛЬКО ДЛЯ КЛИЕНТОВ)
// ============================================
Route::middleware(['auth', 'verified', 'verified.phone', 'check.role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [App\Http\Controllers\Client\AppointmentController::class, 'index'])->name('appointments');
        Route::post('/appointments/{appointment}/cancel', [App\Http\Controllers\Client\AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::get('/history', [App\Http\Controllers\Client\AppointmentController::class, 'history'])->name('history');
        Route::get('/search', [App\Http\Controllers\Client\SearchController::class, 'index'])->name('search');
    });

// ============================================
// БИЗНЕС-ЧАСТЬ (ТОЛЬКО ДЛЯ БИЗНЕСА)
// ============================================
Route::middleware(['auth', 'verified', 'verified.phone', 'check.role:business'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('businesses')->name('businesses.')->group(function () {
        Route::get('/create', [WebBusinessController::class, 'create'])->name('create');
        Route::post('/', [WebBusinessController::class, 'store'])->name('store');
        Route::get('/select', [WebBusinessController::class, 'select'])->name('select');
    });

    Route::middleware(['business.owner'])->group(function () {
        Route::prefix('businesses')->name('businesses.')->group(function () {
            Route::get('/{business}/edit', [WebBusinessController::class, 'edit'])->name('edit');
            Route::put('/{business}', [WebBusinessController::class, 'update'])->name('update');
            Route::delete('/{business}', [WebBusinessController::class, 'destroy'])->name('destroy');
            Route::post('/{business}/switch', [WebBusinessController::class, 'switch'])->name('switch');
        });

        Route::resource('services', WebServiceController::class)->except(['show']);
        Route::get('services/{service}', [WebServiceController::class, 'show'])->name('services.show');
        Route::post('services/{service}/toggle-active', [WebServiceController::class, 'toggleActive'])->name('services.toggle-active');
        Route::post('services/reorder', [WebServiceController::class, 'reorder'])->name('services.reorder');

        Route::resource('employees', WebEmployeeController::class);
        Route::post('employees/{employee}/toggle-active', [WebEmployeeController::class, 'toggleActive'])->name('employees.toggle-active');
        Route::get('employees/{employee}/schedule', [WebEmployeeController::class, 'schedule'])->name('employees.schedule');
        Route::put('employees/{employee}/schedule', [WebEmployeeController::class, 'updateSchedule'])->name('employees.schedule.update');

        Route::resource('clients', WebClientController::class);
        Route::get('clients/export', [WebClientController::class, 'export'])->name('clients.export');
        Route::post('clients/import', [WebClientController::class, 'import'])->name('clients.import');
        Route::get('clients/{client}/history', [WebClientController::class, 'history'])->name('clients.history');

        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [WebAppointmentController::class, 'index'])->name('index');
            Route::get('/calendar/data', [WebAppointmentController::class, 'calendarData'])->name('calendar.data');
            Route::get('/calendar', [WebAppointmentController::class, 'calendar'])->name('calendar');
            Route::get('/create', [WebAppointmentController::class, 'create'])->name('create');
            Route::post('/', [WebAppointmentController::class, 'store'])->name('store');

            Route::get('/{appointment}', [WebAppointmentController::class, 'show'])
                ->where('appointment', '[0-9]+')->name('show');
            Route::get('/{appointment}/edit', [WebAppointmentController::class, 'edit'])
                ->where('appointment', '[0-9]+')->name('edit');
            Route::put('/{appointment}', [WebAppointmentController::class, 'update'])
                ->where('appointment', '[0-9]+')->name('update');
            Route::delete('/{appointment}', [WebAppointmentController::class, 'destroy'])
                ->where('appointment', '[0-9]+')->name('destroy');
            Route::post('/{appointment}/confirm', [WebAppointmentController::class, 'confirm'])
                ->where('appointment', '[0-9]+')->name('confirm');
            Route::post('/{appointment}/cancel', [WebAppointmentController::class, 'cancel'])
                ->where('appointment', '[0-9]+')->name('cancel');
            Route::post('/{appointment}/complete', [WebAppointmentController::class, 'complete'])
                ->where('appointment', '[0-9]+')->name('complete');
            Route::post('/{appointment}/reschedule', [WebAppointmentController::class, 'reschedule'])
                ->where('appointment', '[0-9]+')->name('reschedule');
            Route::post('/{appointment}/send-reminder', [WebAppointmentController::class, 'sendReminder'])
                ->where('appointment', '[0-9]+')->name('send-reminder');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/services', [ReportController::class, 'services'])->name('services');
            Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
            Route::get('/clients', [ReportController::class, 'clients'])->name('clients');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general');
            Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
            Route::put('/integration', [SettingsController::class, 'updateIntegration'])->name('integration');
            Route::post('/logo', [SettingsController::class, 'uploadLogo'])->name('upload-logo');
            Route::delete('/logo', [SettingsController::class, 'deleteLogo'])->name('delete-logo');
        });

        Route::prefix('api')->name('api.')->group(function () {
            Route::post('/clients', [WebClientController::class, 'store'])->name('clients.store');
            Route::get('/clients/search', [WebClientController::class, 'search'])->name('clients.search');
            Route::get('/employees/{employee}/services', [WebEmployeeController::class, 'getServices'])->name('employees.services');
            Route::get('/appointments/slots', [WebAppointmentController::class, 'getAvailableSlots'])->name('appointments.slots');
            Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        });
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});
