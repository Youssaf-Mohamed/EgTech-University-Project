<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function clearMedia()
    {
        $this->deleteFiles(storage_path('app/public'));

        Media::truncate();

        return response()->json(['message' => 'Media cleared successfully']);
    }

    public function cleanupOrphanedFiles()
    {
        $mediaFiles = Media::pluck('file_name')->toArray();

        $storagePath = storage_path('app/public');

        $this->deleteOrphanedFiles($storagePath, $mediaFiles);

        return response()->json(['message' => 'Orphaned files cleaned up successfully']);
    }

    private function deleteFiles($directory)
    {
        if (is_dir($directory)) {
            $files = glob($directory . '/*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                } elseif (is_dir($file)) {
                    $this->deleteFiles($file);
                }
            }
            rmdir($directory);
        }
    }

    private function deleteOrphanedFiles($directory, $mediaFiles)
    {
        if (is_dir($directory)) {
            $files = glob($directory . '/*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileName = basename($file);

                    if (!in_array($fileName, $mediaFiles)) {
                        unlink($file);
                        $this->info(" Deleted orphaned file: $fileName");
                    }
                } elseif (is_dir($file)) {
                    $this->deleteOrphanedFiles($file, $mediaFiles);
                }
            }
        }
    }
}
