<?php

declare(strict_types=1);

namespace App\Http\Controllers\FileStorage\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;

class StorageService
{
    private Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }

    public function getFilesFromPath(string $path): array
    {
        $files = $this->disk->files($this->createPath('', $path));
        $dirs = $this->disk->directories($this->createPath('', $path));

        $filePaths = ['directory' => $dirs, 'file' => $files];
        $paths = [];
        foreach ($filePaths as $type => $bucketPaths) {
            foreach ($bucketPaths as $path) {
                $path = $this->excludeUserRoot($path);
                $paths[$type][] = $path;
            }
        }

        return $paths;
    }


    public function getUserBucket(): string
    {
        return "user-" . Auth::id() . "-files";
    }


    public function createPath(string $path, string $name): string
    {
        if ($path === '') {
            $newPath = implode('/', [$this->getUserBucket(), $name]);
        } else {
            $newPath = implode('/', [$this->getUserBucket(), $path, $name]);
        }

        return $newPath;
    }


    private function excludeUserRoot(string $path): string
    {
        return substr($path, strpos($path, '/') + 1);
    }

}
