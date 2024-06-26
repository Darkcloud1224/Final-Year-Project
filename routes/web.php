<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetRecommendationController;
use App\Http\Controllers\ReportGenerationController;
use App\Http\Controllers\ReportLogController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApprovalLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeleteRequestController;
use App\Http\Controllers\DeleteRequestLogController;
use App\Http\Controllers\SwitchgearClassificationController;
use App\Http\Controllers\SwitchgearProgressMonitoringController;

URL::forceScheme('https');
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/asset_recommendation', [AssetRecommendationController::class, 'index'])->name('asset_recommendation');
    Route::post('assets/{id}/acknowledge', [AssetRecommendationController::class, 'acknowledge'])->name('assets.acknowledge');
    Route::post('assets/{id}/update-status', [AssetRecommendationController::class, 'updateStatus'])->name('assets.updateStatus');
    Route::get('/export', [AssetRecommendationController::class, 'export'])->name('export');
    Route::post('/import', [AssetRecommendationController::class, 'import'])->name('import');
    Route::resource('report_generation', ReportGenerationController::class);
    Route::resource('report_log', ReportLogController::class);
    Route::get('/delete_request_logs', [DeleteRequestLogController::class, 'index'])->name('delete_request_logs.index');
    Route::get('/switchgear-classification', [SwitchgearClassificationController::class, 'index'])->name('switchgear_classification.index');
    Route::get('/switchgear-progress-monitoring', [SwitchgearProgressMonitoringController::class, 'index'])->name('switchgear_progress_monitoring.index');
    Route::resource('approval_log', ApprovalLogController::class);
    Route::post('/delete/{id}', [AssetRecommendationController::class, 'delete'])->name('assets.delete');


    Route::middleware(['role:admin'])->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approval/{id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('/approval/{id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
        Route::get('/delete_requests', [DeleteRequestController::class, 'index'])->name('delete_requests.index');
        Route::post('/approve/{id}', [DeleteRequestController::class, 'approveDeleteRequest'])->name('delete_requests.approve');
        Route::post('/reject/{id}', [DeleteRequestController::class, 'rejectDeleteRequest'])->name('delete_requests.reject');
        Route::resource('users', AdminController::class);
    });

});
