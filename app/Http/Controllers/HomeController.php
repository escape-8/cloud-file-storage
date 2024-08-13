<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FileStorage\Services\StorageService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->middleware('auth');
        $this->storageService = $storageService;
    }

    public function index(Request $request)
    {
        $path = $request->query('path', '');
        $files = $this->storageService->getFilesFromPath(urldecode($path));
        return view('home', ['files' => $files]);
    }
}
