<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function replace(?UploadedFile $file, ?string $oldPath, string $directory): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $newPath = $file->store($directory, 'public');

        if ($newPath && $oldPath && $oldPath !== $newPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $newPath;
    }
}
