<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\File;

Route::get('/reset-database', function () {
    try {
        Artisan::call('migrate:fresh', ['--seed' => true]);

        return response()->json([
            'message' => 'Database reset and seeded successfully!',
            'output' => Artisan::output()
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error resetting database!',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
});

Route::get('/start-queue', function () {
    try {
        if (app()->environment('production')) {
            return response()->json(['message' => 'This action is not allowed in production!'], 403);
        }

        shell_exec('php artisan queue:work --daemon > /dev/null 2>&1 &');

        return response()->json([
            'message' => 'Queue worker started in the background!'
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error resetting database!',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
});



Route::get('/clear-media', [MediaController::class, 'clearMedia'])->name('clear.media');
Route::get('/cleanup-unused-files', [MediaController::class, 'cleanupOrphanedFiles'])->name('cleanup.unused.files');


Route::post('/storage-link', function () {
    try {
        Artisan::call('storage:link');

        return response()->json([
            'message' => 'Storage link created successfully!',
            'output' => Artisan::output()
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error creating storage link!',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
});

Route::get('/reset-storage-link', function () {
    try {
        // 1. حذف الرابط الرمزي الحالي إذا كان موجودًا
        $storagePath = public_path('storage');
        if (File::exists($storagePath)) {
            File::deleteDirectory($storagePath);
        }

        // 2. إعادة إنشاء الرابط الرمزي
        Artisan::call('storage:link');

        return response()->json([
            'message' => 'Storage link reset and created successfully!',
            'output' => Artisan::output()
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error resetting storage link!',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
});
