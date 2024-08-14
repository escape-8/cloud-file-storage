<?php

declare(strict_types=1);

namespace App\Http\Controllers\FileStorage\Services;

use App\Http\Controllers\FileStorage\Exceptions\FileNameCollisionException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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

    public function createDirectory(string $path): void
    {
        if ($this->disk->exists($path)) {
            $newDirName = last(explode('/', $path));
            throw new FileNameCollisionException("Directory $newDirName already exists, please, rename it");
        }

        $this->disk->makeDirectory($path);
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

    public function downloadFile(string $pathToFile): BinaryFileResponse|StreamedResponse
    {
        $fullPath = $this->createPath('', $pathToFile);

        if ($this->disk->directoryExists($fullPath)) {
            $zip = new ZipArchive();
            $filename = last(explode('/', $pathToFile)) . '.zip';
            $zip->open($filename, ZipArchive::CREATE);
            $zip->addEmptyDir($pathToFile);
            $this->packDirectory($fullPath, $zip);
            $zip->close();

            return response()->download(public_path($filename))->deleteFileAfterSend();
        }

        return $this->disk->download($fullPath);
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

    private function packDirectory(string $pathOfDirectory, ZipArchive $zip): ZipArchive
    {

        foreach ($this->disk->listContents($pathOfDirectory) as $file) {
            if ($file['type'] === 'file') {
                $zipFilepath = $this->excludeUserRoot($file['path']);
                $zip->addFromString($zipFilepath, $this->disk->read($file['path']));
            }

            if ($file['type'] === 'dir') {
                $this->packDirectory($file['path'], $zip);
            }
        }

        return $zip;
    }


    private function excludeUserRoot(string $path): string
    {
        return substr($path, strpos($path, '/') + 1);
    }

}
