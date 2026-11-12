<?php

use App\Http\Controllers\Apply\ApplicationController;
use App\Http\Controllers\Apply\GateController;
use App\Http\Controllers\DocumentResubmitController;
use App\Http\Controllers\InterestRegistrationController;
use App\Http\Controllers\ThanksController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::post('/register-interest', [InterestRegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('interest.store');

Route::get('/apply', [GateController::class, 'show'])->name('apply');
Route::post('/apply/gate', [GateController::class, 'authenticate'])
    ->middleware('throttle:10,1')
    ->name('apply.gate');
Route::post('/apply/logout', [GateController::class, 'logout'])->name('apply.logout');

Route::middleware('applicant')->prefix('apply')->name('apply.')->group(function () {
    Route::get('/application', [ApplicationController::class, 'show'])->name('application');
    Route::post('/application/step-1', [ApplicationController::class, 'storeStep1'])
        ->middleware('throttle:30,1')
        ->name('application.step1');
    Route::post('/application/step-2', [ApplicationController::class, 'storeStep2'])
        ->middleware('throttle:30,1')
        ->name('application.step2');
    Route::post('/application/step-3', [ApplicationController::class, 'storeStep3'])
        ->middleware('throttle:20,1')
        ->name('application.step3');
});

Route::get('/documents/resubmit/{token}', [DocumentResubmitController::class, 'show'])
    ->name('documents.resubmit');
Route::get('/documents/resubmit/{token}/files/{type}', [DocumentResubmitController::class, 'file'])
    ->whereIn('type', ['certificate_80g', 'certificate_12a', 'section_8', 'coi', 'pan', 'gst'])
    ->name('documents.resubmit.file');
Route::post('/documents/resubmit/{token}', [DocumentResubmitController::class, 'update'])
    ->middleware('throttle:10,1')
    ->name('documents.resubmit.update');

Route::get('/thanks', ThanksController::class)->name('thanks');
