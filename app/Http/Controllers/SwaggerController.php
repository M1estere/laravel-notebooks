<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function show()
    {
        $filePath = storage_path('api-docs/api-docs.json');

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $jsonContent = File::get($filePath);
        $decodedJson = json_decode($jsonContent, true);

        return view('swagger', ['swaggerJson' => $decodedJson]);
    }
}
