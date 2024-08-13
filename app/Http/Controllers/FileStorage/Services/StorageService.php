<?php

declare(strict_types=1);

namespace App\Http\Controllers\FileStorage\Services;

use App\Http\Controllers\FileStorage\Exceptions\FileNameCollisionException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function uploadFile(array|UploadedFile|null $files, string $path): void
    {
        foreach ($files as $index => $file) {
            $fileFullPath = $_FILES['files']['full_path'][$index];
            $bucketPath = $this->createPath($path, $fileFullPath);

            if ($this->disk->exists($bucketPath)) {
                $name = Str::limit($file->getClientOriginalName(), 10);
                throw new FileNameCollisionException("Name $name already exists, please, rename it and reload");
            }

            $this->disk->put(
                $bucketPath,
                file_get_contents($file->getRealPath())
            );
        }
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
