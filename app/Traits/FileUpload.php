<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileUpload
{
    public function uploadFile(UploadedFile $file, string $path, string $filename)
    {
        $filename = $filename.".".$file->getClientOriginalExtension();
        $file->storeAs($path, $filename);
        // Storage::disk('s3')->putFileAs($path, $file, $filename);

        return $path . '/' . $filename;
    }
}
