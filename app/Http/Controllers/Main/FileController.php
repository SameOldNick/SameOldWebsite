<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Responds with file contents
     *
     * @return mixed
     */
    public function retrieve(Request $request, File $file)
    {
        return Storage::disk($file->disk)->response($file->path, $file->name);
    }
}
