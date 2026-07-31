<?php
// routes/api.php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BusinessController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\ReportController as ApiReportController;
use App\Http\Controllers\Api\V1\SettingsController as ApiSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ============================================
    // Публичные API маршруты (без аутентификации)
    // ============================================
    Route::prefix('public')->group(function () {
        Route::get('/booking/{businessSlug}/services', [AppointmentController::class, 'publicServices']);
        Route::get('/booking/{businessSlug}/employees', [AppointmentController::class, 'publicEmployees']);
        Route::get('/booking/{businessSlug}/slots', [AppointmentController::class, 'publicAvailableSlots']);
        Route::post('/booking/appointments', [AppointmentController::class, 'publicStore']);
        Route::get('/booking/appointments/confirm/{token}', [AppointmentController::class, 'publicConfirm']);
        Route::get('/booking/appointments/cancel/{token}', [AppointmentController::class, 'publicCancel']);
        Route::get('/businesses/{businessSlug}', [BusinessController::class, 'publicShow']);
        Route::get('/calendar-slots', [App\Http\Controllers\Public\BookingController::class, 'calendarSlots'])->name('public.calendar-slots');
    });

    // ============================================
    // Аутентификация
    // ============================================
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/send-verification', [AuthController::class, 'sendVerificationCode']);
    Route::post('/auth/verify-phone', [AuthController::class, 'verifyPhone']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // ============================================
    // Защищенные API маршруты (требуют аутентификации)
    // ============================================
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);

    // ============================================
    // Бизнес (Business)
    // ============================================
    Route::prefix('businesses')->group(function () {
        Route::get('/', [BusinessController::class, 'index']);
        Route::post('/', [BusinessController::class, 'store']);
        Route::get('/{business}', [BusinessController::class, 'show']);
        Route::put('/{business}', [BusinessController::class, 'update']);
        Route::delete('/{business}', [BusinessController::class, 'destroy']);
        Route::post('/{business}/switch', [BusinessController::class, 'switch']);
        Route::get('/{business}/stats', [BusinessController::class, 'stats']);
    });

    // ============================================
    // Услуги (Services)
    // ============================================
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/{service}', [ServiceController::class, 'show']);
        Route::put('/{service}', [ServiceController::class, 'update']);
        Route::delete('/{service}', [ServiceController::class, 'destroy']);
        Route::post('/{service}/toggle', [ServiceController::class, 'toggleActive']);
        Route::post('/reorder', [ServiceController::class, 'reorder']);
        Route::get('/{service}/employees', [ServiceController::class, 'getEmployees']);
    });

    // ============================================
    // Сотрудники (Employees)
    // ============================================
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{employee}', [EmployeeController::class, 'show']);
        Route::put('/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
        Route::post('/{employee}/toggle', [EmployeeController::class, 'toggleActive']);
        Route::get('/{employee}/services', [EmployeeController::class, 'getServices']);
        Route::get('/{employee}/schedule', [EmployeeController::class, 'getSchedule']);
        Route::put('/{employee}/schedule', [EmployeeController::class, 'updateSchedule']);
        Route::get('/{employee}/appointments', [EmployeeController::class, 'getAppointments']);
    });

    // ============================================
    // Клиенты (Clients) - ОДИН РАЗ!
    // ============================================
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{client}', [ClientController::class, 'show']);
        Route::put('/{client}', [ClientController::class, 'update']);
        Route::delete('/{client}', [ClientController::class, 'destroy']);
        Route::get('/search', [ClientController::class, 'search']);
        Route::get('/{client}/history', [ClientController::class, 'history']);
        Route::get('/export', [ClientController::class, 'export']);
        Route::post('/import', [ClientController::class, 'import']);
        Route::post('/{client}/send-promo', [ClientController::class, 'sendPromo']);
    });

    // ============================================
    // Записи (Appointments)
    // ============================================
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/{appointment}', [AppointmentController::class, 'show']);
        Route::put('/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy']);

        Route::post('/{appointment}/confirm', [AppointmentController::class, 'confirm']);
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('/{appointment}/complete', [AppointmentController::class, 'complete']);
        Route::post('/{appointment}/reschedule', [AppointmentController::class, 'reschedule']);
        Route::post('/{appointment}/send-reminder', [AppointmentController::class, 'sendReminder']);
        Route::post('/{appointment}/add-note', [AppointmentController::class, 'addNote']);

        Route::get('/available/slots', [AppointmentController::class, 'availableSlots']);
        Route::get('/calendar', [AppointmentController::class, 'calendarData']);
        Route::get('/upcoming', [AppointmentController::class, 'upcoming']);
        Route::get('/statistics', [AppointmentController::class, 'statistics']);
    });

    // ============================================
    // Отчеты (Reports)
    // ============================================
    Route::prefix('reports')->group(function () {
        Route::get('/', [ApiReportController::class, 'index']);
        Route::get('/revenue', [ApiReportController::class, 'revenue']);
        Route::get('/services', [ApiReportController::class, 'services']);
        Route::get('/employees', [ApiReportController::class, 'employees']);
        Route::get('/clients', [ApiReportController::class, 'clients']);
        Route::get('/export', [ApiReportController::class, 'export']);
    });

    // ============================================
    // Уведомления (Notifications)
    // ============================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::post('/mark-read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markRead']);
        Route::post('/mark-all-read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllRead']);
        Route::delete('/{notification}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);
        Route::get('/counts', [\App\Http\Controllers\Api\V1\NotificationController::class, 'counts']);
    });

    // ============================================
    // Настройки (Settings)
    // ============================================
    Route::prefix('settings')->group(function () {
        Route::get('/', [ApiSettingsController::class, 'index']);
        Route::put('/general', [ApiSettingsController::class, 'updateGeneral']);
        Route::put('/notifications', [ApiSettingsController::class, 'updateNotifications']);
        Route::put('/integration', [ApiSettingsController::class, 'updateIntegration']);
        Route::post('/logo', [ApiSettingsController::class, 'uploadLogo']);
        Route::delete('/logo', [ApiSettingsController::class, 'deleteLogo']);
        Route::get('/widget', [ApiSettingsController::class, 'widget']);
        Route::get('/widget/embed', [ApiSettingsController::class, 'widgetEmbed']);
    });

    // ============================================
    // Рабочие часы (Working Hours)
    // ============================================
    Route::prefix('working-hours')->group(function () {
        Route::post('/batch', [\App\Http\Controllers\Api\V1\WorkingHourController::class, 'batchUpdate']);
        Route::post('/copy-week', [\App\Http\Controllers\Api\V1\WorkingHourController::class, 'copyWeek']);
        Route::get('/employee/{employee}', [\App\Http\Controllers\Api\V1\WorkingHourController::class, 'getByEmployee']);
        Route::get('/business', [\App\Http\Controllers\Api\V1\WorkingHourController::class, 'getBusinessWorkingHours']);
    });
});
