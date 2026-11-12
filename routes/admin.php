<?php

use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\ApplicantFileController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\PageTemplateController;
use App\Http\Controllers\Admin\ScoreboardController;
use App\Http\Controllers\Admin\ShortlistController;
use App\Http\Middleware\EnsureShortlistEnabled;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware(EnsureShortlistEnabled::class)->group(function () {
        Route::get('scoreboard', ScoreboardController::class)->name('scoreboard');
        Route::get('shortlist', [ShortlistController::class, 'index'])->name('shortlist.index');
        Route::post('shortlist', [ShortlistController::class, 'store'])->name('shortlist.store');
        Route::put('shortlist/{entry}', [ShortlistController::class, 'update'])->name('shortlist.update');
        Route::patch('shortlist/{entry}/status', [ShortlistController::class, 'status'])->name('shortlist.status');
        Route::patch('shortlist/{entry}/move', [ShortlistController::class, 'move'])->name('shortlist.move');
        Route::delete('shortlist/{entry}', [ShortlistController::class, 'destroy'])->name('shortlist.destroy');
        Route::get('shortlist/{entry}/logo', [ShortlistController::class, 'logo'])->name('shortlist.logo');
    });

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
