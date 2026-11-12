<?php

use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\ApplicantFileController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\PageTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');

    Route::get('page-templates', [PageTemplateController::class, 'index'])->name('page-templates.index');
    Route::get('page-templates/{pageTemplate}/edit', [PageTemplateController::class, 'edit'])->name('page-templates.edit');
    Route::put('page-templates/{pageTemplate}', [PageTemplateController::class, 'update'])->name('page-templates.update');

    Route::get('applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('applicants/data', [ApplicantController::class, 'data'])->name('applicants.data');
    Route::get('applicants/export', [ApplicantController::class, 'export'])->name('applicants.export');
    Route::get('applicants/{applicant}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::get('applicants/{applicant}/files/{type}', [ApplicantFileController::class, 'show'])
        ->whereIn('type', ['certificate_80g', 'certificate_12a', 'section_8', 'coi', 'pan', 'gst', 'deck'])
        ->name('applicants.files.show');
    Route::post('applicants/{applicant}/invite', [ApplicantController::class, 'invite'])->name('applicants.invite');
    Route::post('applicants/{applicant}/resubmit-documents', [ApplicantController::class, 'requestDocumentResubmit'])->name('applicants.resubmit-documents');
    Route::post('applicants/{applicant}/reject', [ApplicantController::class, 'reject'])->name('applicants.reject');
});
