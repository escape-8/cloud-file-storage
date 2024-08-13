<?php

declare(strict_types=1);

namespace App\Http\Controllers\FileStorage\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class StorageService
{
    private Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }
}
