<?php

namespace App\Http\Controllers\Main;

use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Responds with file contents
     *
     * @param File $file
     * @return mixed
     */
    public function retrieve(Request $request, File $file)
    {
        return Storage::response($file->path, $file->name);
    }
}
