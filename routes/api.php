<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaysOffController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\UserNotificationController;

// Authenticacion
Route::post('/login',    [AuthController::class, 'login']);
// Registro sin autenticacion
Route::post('/register', [AuthController::class, 'register']);

//Autenticacion con rol usuarios
Route::middleware(['auth:sanctum'])->group(function () {
    //Cerrar la sesion no importa el rol 
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    Route::get('/schedules/available', [ScheduleController::class, 'available']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/user/appointments', [AppointmentController::class, 'userAppointments']);
    Route::put('user/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);


    Route::get('user/notifications/check-new', [UserNotificationController::class, 'checkNew']);
});



//******** Rutas con middleware admin *******/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew']);


    Route::apiResource('users', UserController::class);
    Route::post('schedules/generate-range', [ScheduleController::class, 'generateRange']);
    Route::apiResource('schedules', ScheduleController::class);

    Route::post('days-off', [DaysOffController::class, 'toggle']);
    Route::get('days-off', [DaysOffController::class, 'show']);





    Route::get('/appointments', [AppointmentController::class, 'adminIndex']);

    Route::put('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm']);

    Route::get('/appointments/check-new', [AppointmentController::class, 'checkNew']);

    Route::get('/appointments/check-cancelled', [AppointmentController::class, 'checkCancelled']);

    Route::put('/appointments/{appointment}/cancel', [AppointmentController::class, 'adminCancel']);

    Route::put('/appointments/{appointment}/attended', [AppointmentController::class, 'markAsAttended']);

});
