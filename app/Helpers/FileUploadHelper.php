<?php

if (!function_exists('uploadFile')) {
    /**
     * Upload a file to the specified directory.
     *
     * @param \Illuminate\Http\UploadedFile $file The file to be uploaded.
     * @param string $directory The directory where the file should be uploaded.
     * @return string|null The path to the uploaded file or null if no file was uploaded.
     */
    function uploadFile($file, $directory )
    {
        if ($file) {
           
            $randomNumber = rand(1000000000, 9999999999);
            $fileName =$randomNumber .'-'. time().'.' . $file->extension();
            $file->move(public_path($directory), $fileName);
            return $fileName;
        }
        return null;
    }
   
    function deleteFile($folder,$name)
    {
        $filePath= public_path($folder) . '/' . $name;
    
        if (\Illuminate\Support\Facades\File::exists($filePath)) {
            return \Illuminate\Support\Facades\File::delete($filePath);
        }
        return false;
    }
}
