<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotebookController extends Controller
{
    public function index(Request $request)
    {
        $notebooks = Notebook::paginate(10);
        return response()->json($notebooks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fio' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
        ]);

        $existingNotebook = Notebook::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->orWhere('fio', $request->fio)
            ->first();

        if ($existingNotebook) {
            return response()->json([
                'message' => 'Notebook with this fio, email or phone already exists.',
            ], 409);
        }

        $notebook = Notebook::create($request->all());
        return response()->json($notebook, 201);
    }

    public function show($id)
    {
        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        return response()->json($notebook);
    }

    public function update(Request $request, $id)
    {
        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        $notebook->update($request->all());
        return response()->json($notebook);
    }

    public function destroy($id)
    {
        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        $notebook->delete();
        return response()->noContent();
    }
}
