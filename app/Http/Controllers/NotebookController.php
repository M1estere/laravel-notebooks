<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Notebook API",
 *     version="1.0.0",
 * )
 */
class NotebookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/notebook",
     *     summary="Get all notebooks",
     *     @OA\Response(response="200", description="Notebooks data")
     * )
     */
    public function index(Request $request)
    {
        $notebooks = Notebook::paginate(10);
        return response()->json($notebooks);
    }

    /**
     * @OA\Post(
     *     path="/notebook",
     *     summary="Create a new notebook",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Notebook")
     *     ),
     *     @OA\Response(response="201", description="Created notebook"),
     *     @OA\Response(response="409", description="Notebook with this fio, email, or phone already exists")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/notebook/{id}",
     *     summary="Get a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *     @OA\Response(response="200", description="Notebook"),
     *     @OA\Response(response="404", description="Notebook not found")
     * )
     */
    public function show($id)
    {
        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        return response()->json($notebook);
    }

    /**
     * @OA\Put(
     *     path="/notebook/{id}",
     *     summary="Update a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *     @OA\Response(response="200", description="Notebook updated"),
     *     @OA\Response(response="404", description="Notebook not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        $notebook->update($request->all());
        return response()->json($notebook);
    }

    /**
     * @OA\Delete(
     *     path="/notebook/{id}",
     *     summary="Delete a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *     @OA\Response(response="200", description="Delete success"),
     *     @OA\Response(response="404", description="Notebook not found")
     * )
     */
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
