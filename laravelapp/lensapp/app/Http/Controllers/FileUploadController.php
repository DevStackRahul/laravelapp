<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FileUploadController extends Controller
{
    public function fileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf'
        ]);

        if ($request->file('file')) {
            $destination_folder = '/uploaded/files/';
            $imageName = time() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move(public_path($destination_folder), $imageName);
            return [
                'file_path' => 'https://prescription.installmultiplepixel.com/lensapp/public' . $destination_folder . $imageName
            ];
        }
    }
}
