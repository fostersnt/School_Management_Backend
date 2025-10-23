<?php

namespace App\Http\Controllers;

use App\Models\ExcelFileReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('home');
    }


    public function fileUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_data' => 'required|mimes:xlsx|max:2024'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors()->all());
        } else {
            $my_file = $request->file('file_data');
            try {
                $file_path = $my_file->store('ExcelData');

                ExcelFileReading::create([
                    'file_description' => 'This is the first excel file',
                    'file_path' => $file_path
                ]);

                return back()->with(['success' => 'File stored successfully for upload']);
            } catch (\Throwable $th) {
                // dd($th->getMessage());
                return back()->withErrors(['errors' => [$th->getMessage()]]);
            }
        }
    }
}
