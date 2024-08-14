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

    public function rename(string $oldPathToFile, string $newFilename): void
    {
        $fullOldPath = $this->createPath('', $oldPathToFile);

        if ($this->disk->directoryExists($fullOldPath)) {
            $newPath = $this->getPathWithoutLastPart($oldPathToFile);
            $newDirPath = $this->createPath($newPath, $newFilename);

            if ($this->disk->exists($newDirPath)) {
                throw new FileNameCollisionException("Directory with name $newFilename already exists, please, rename it");
            }

            $this->createDirectory($newDirPath);

            $filesInOldDir = $this->disk->allFiles($fullOldPath);
            $dirsInOldDir = $this->disk->allDirectories($fullOldPath);


            foreach ($dirsInOldDir as $oldDir) {
                $innerDirName = str_replace($fullOldPath, $newDirPath , $oldDir);
                $this->createDirectory($innerDirName);
            }

            foreach ($filesInOldDir as $file) {
                $innerFileName = str_replace($fullOldPath, $newDirPath , $file);
                $this->disk->move($file, $innerFileName);
            }

            $this->delete($this->excludeUserRoot($fullOldPath));

        } else {
            $newPath = $this->getPathWithoutLastPart($fullOldPath) . '/' . $newFilename;

            if ($this->disk->exists($newPath)) {
                throw new FileNameCollisionException("File with name $newFilename already exists, please, rename it");
            }

            $this->disk->move($fullOldPath, $newPath);
        }

    }

    public function delete(string $pathToFile): bool
    {
        $fullPath = $this->createPath('', $pathToFile);

        if ($this->disk->directoryExists($fullPath)) {
            return $this->disk->deleteDirectory($fullPath);
        }

        return $this->disk->delete($fullPath);
    }

    public function search(string $search): array
    {
        $fileNames = $this->disk->allFiles($this->getUserBucket());
        $dirsPath = $this->disk->allDirectories($this->getUserBucket());

        $filtered = collect(['dirs' => $dirsPath, 'files' => $fileNames])
            ->map(function ($paths) use ($search) {
                return collect($paths)->filter(function ($path) use ($search) {
                    return str_contains($this->excludeUserRoot($path), $search);
                })->values();
            });

        $out = $filtered->map(function ($paths, $type) {
            return $type === 'files'
                ? collect($paths)
                    ->keyBy(fn ($path) => $this->excludeUserRoot($path))
                    ->map(fn ($path, $keyFilename) => route('home', ['path' => $this->getPathWithoutLastPart($keyFilename)]))
                    ->toArray()
                : collect($paths)
                    ->keyBy(fn ($path) => $this->excludeUserRoot($path))
                    ->map(fn ($path, $keyFilename) => route('home', ['path' => $keyFilename]))
                    ->toArray();
        });

        return $out->toArray();
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

    private function getLastPartOfPath(string $path): string
    {
        return substr($path, strrpos($path, '/') + 1);
    }

    private function getPathWithoutLastPart(string $path): string
    {
        return substr($path, 0, intval(strrpos($path, '/')));
    }

    private function excludeUserRoot(string $path): string
    {
        return substr($path, strpos($path, '/') + 1);
    }

}
