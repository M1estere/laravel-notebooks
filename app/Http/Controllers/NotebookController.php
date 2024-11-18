<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Validator;

/**
 * @OA\Info(
 *     title="Notebook API",
 *     version="1.0.0",
 * )
 */
class NotebookController extends Controller
{
    /**
     * Checks database connection
     *
     * @return bool
     */
    private function isDatabaseConnected()
    {
        return DB::connection()->getDatabaseName() !== null;
    }

    /**
     * @OA\Schema(
     *       schema="AllData",
     *       type="object",
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Notebook")),
     *       @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *       @OA\Property(property="from", type="integer", example=1),
     *       @OA\Property(property="last_page", type="integer", example=1),
     *       @OA\Property(property="last_page_url", type="string", format="url", example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *       @OA\Property(property="links", type="array",
     *           @OA\Items(type="object",
     *               @OA\Property(property="url", type="string", format="url", example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *               @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *               @OA\Property(property="active", type="boolean", example=false),
     *           )
     *       ),
     *       @OA\Property(property="next_page_url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *       @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *       @OA\Property(property="per_page", type="integer", example=10),
     *       @OA\Property(property="prev_page_url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v1/notebook?page=1"),
     *       @OA\Property(property="to", type="integer", example=2),
     *       @OA\Property(property="total", type="integer", example=2),
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/notebook",
     *     summary="Get all notebooks",
     *
     *     @OA\Response(
     *          response="200",
     *          description="Notebooks data",
     *          @OA\JsonContent(ref="#/components/schemas/AllData")
     *     ),
     *     @OA\Response(response="500", description="Database connection error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Database connection error"),
     *          )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if (!$this->isDatabaseConnected()) {
            return response()->json([
                'message' => 'Database connection error'
            ], 500);
        }

        $notebooks = Notebook::paginate(10);
        return response()->json($notebooks);
    }

    /**
     * @OA\Schema(
     *     schema="Notebook",
     *     type="object",
     *     @OA\Property(property="fio", type="string", example="Testov Test Testovich"),
     *     @OA\Property(property="company", type="string", example="Test Company"),
     *     @OA\Property(property="phone", type="string", example="88888888888"),
     *     @OA\Property(property="email", type="string", example="test@example.com"),
     *     @OA\Property(property="birthdate", type="string", format="date", example="2003-04-15"),
     *     @OA\Property(property="photo", type="string", format="url", example="https://example.com/photo.jpg")
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/v1/notebook",
     *     summary="Create a new notebook",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Notebook")
     *     ),
     *
     *     @OA\Response(
     *         response="201",
     *         description="Created notebook",
     *         @OA\JsonContent(ref="#/components/schemas/Notebook")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Invalid input data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid input data"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="409",
     *          description="Notebook with this fio, email, or phone already exists",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Notebook with this fio, email, or phone already exists"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Database connection error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Database connection error"),
     *          )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!$this->isDatabaseConnected()) {
            return response()->json([
                'message' => 'Database connection error'
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'fio' => 'required|string',
            'company' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'birthdate' => 'nullable|date',
            'photo' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input data'
            ], 400);
        }

        $existingNotebook = Notebook::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->orWhere('fio', $request->fio)
            ->first();

        if ($existingNotebook) {
            return response()->json([
                'message' => 'Notebook with this fio, email or phone already exists',
            ], 409);
        }

        $notebook = Notebook::create($request->all());
        return response()->json($notebook, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/notebook/{id}",
     *     summary="Get a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Notebook",
     *         @OA\JsonContent(ref="#/components/schemas/Notebook")
     *     ),
     *     @OA\Response(response="404", description="Notebook not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Notebook not found"),
     *          )
     *     ),
     *     @OA\Response(response="500", description="Database connection error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Database connection error"),
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        if (!$this->isDatabaseConnected()) {
            return response()->json([
                'message' => 'Database connection error'
            ], 500);
        }

        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        return response()->json($notebook);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/notebook/{id}",
     *     summary="Update a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Notebook updated",
     *         @OA\JsonContent(ref="#/components/schemas/Notebook")
     *     ),
     *     @OA\Response(response="400", description="Invalid input data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid input data"),
     *          )
     *     ),
     *     @OA\Response(response="404", description="Notebook not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Notebook not found"),
     *          )
     *     ),
     *     @OA\Response(response="500", description="Database connection error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Database connection error"),
     *          )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (!$this->isDatabaseConnected()) {
            return response()->json([
                'message' => 'Database connection error'
            ], 500);
        }

        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fio' => 'required|string',
            'company' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'birthdate' => 'nullable|date',
            'photo' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input data'
            ], 400);
        }

        $notebook->update($request->all());
        return response()->json($notebook);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/notebook/{id}",
     *     summary="Delete a notebook by ID",
     *     @OA\Parameter(name="id", in="path", description="ID of the notebook"),
     *
     *     @OA\Response(response="204", description="Delete success"),
     *     @OA\Response(response="404", description="Notebook not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Notebook not found"),
     *          )
     *     ),
     *     @OA\Response(response="500", description="Database connection error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Database connection error"),
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        if (!$this->isDatabaseConnected()) {
            return response()->json([
                'message' => 'Database connection error'
            ], 500);
        }

        $notebook = Notebook::find($id);
        if ($notebook === null) {
            return response()->json(['message' => 'Notebook not found'], 404);
        }

        $notebook->delete();
        return response()->noContent();
    }
}
