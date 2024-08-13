<?php

namespace App\Http\Controllers\FileStorage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileStorage\Exceptions\FileNameCollisionException;
use App\Http\Controllers\FileStorage\Services\StorageService;
use App\Http\Requests\UploadFileRequest;

class DiskController extends Controller
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->middleware('auth');
        $this->storageService = $storageService;
    }
    public function createDirectory(CreateDirectoryRequest $request): JsonResponse
    {
        $pathToDir = $this->storageService->createPath(urldecode($request->get('path')) ?? '', $request->get('name'));

        try {
            $this->storageService->createDirectory($pathToDir);
        } catch (FileNameCollisionException $e) {
            return response()->json(['errors' => ['name' => [$e->getMessage()]]], $e->getCode());
        }

        return response()->json(['status' => 'Add directory successfully!']);
    }

    public function upload(UploadFileRequest $request): JsonResponse
    {
        $files = $request->file('files');

        try {
            $this->storageService->uploadFile($files, urldecode($request->get('path') ?? ''));
        } catch (FileNameCollisionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return response()->json(['status' => 'OK']);
    }

    public function download(Request $request): BinaryFileResponse|StreamedResponse
    {
        $pathToFile = $request->query('path');
        return $this->storageService->downloadFile($pathToFile);
    }

    }
}
