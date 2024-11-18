<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function show()
    {
        // Путь к файлу swagger.json
        $filePath = storage_path('api-docs/api-docs.json');

        // Проверяем, существует ли файл
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Читаем содержимое файла
        $jsonContent = File::get($filePath);
        $decodedJson = json_decode($jsonContent, true);

        // Передаем содержимое в представление
        return view('swagger', ['swaggerJson' => $decodedJson]);
    }
}
