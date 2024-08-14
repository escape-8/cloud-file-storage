<?php

namespace App\Http\Controllers\FileStorage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileStorage\Exceptions\FileNameCollisionException;
use App\Http\Controllers\FileStorage\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\UploadFileRequest;

class DiskController extends Controller
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->middleware('auth');
        $this->storageService = $storageService;
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
