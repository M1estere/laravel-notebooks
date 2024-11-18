<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\Request;

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

        $notebook = Notebook::create($request->all());
        return response()->json($notebook, 201);
    }

    public function show($id)
    {
        $notebook = Notebook::findOrFail($id);
        return response()->json($notebook);
    }

    public function update(Request $request, $id)
    {
        $notebook = Notebook::findOrFail($id);
        $notebook->update($request->all());
        return response()->json($notebook);
    }

    public function destroy($id)
    {
        Notebook::destroy($id);
        return response()->json(null, 204);
    }
}
