<?php

namespace App\Http\Controllers\FileStorage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileStorage\Services\StorageService;

class DiskController extends Controller
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->middleware('auth');
        $this->storageService = $storageService;
    }
}
