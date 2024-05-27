<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetRecommendationController;
use App\Http\Controllers\ReportGenerationController;
use App\Http\Controllers\ReportLogController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApprovalLogController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    Route::resource('approval_log', ApprovalLogController::class);
    });
