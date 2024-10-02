<?php

namespace App\Http\Controllers;

use App\Models\Operators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OperatorsController extends Controller
{
    /**
     *  List of all Operators
     * @OA\Get (
     *     path="/api/operators",
     *     tags={"Operators"},
     *     security={{"passport": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="Anibal Gallardo"),
     *                     @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $get_data = Operators::all();

            if (!$get_data) {
                return $this->sendResponse([], "Operators not found", 404);
            }

            $data = $get_data->map(function ($operator) {
                return [
                    "id" => $operator->id,
                    "name" => $operator->name,
                    "created_at" => $operator->created_at,
                ];
            });

            return $this->sendResponse($data, "Operators retrieve successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([],  $e->getMessage(), 500);
        }
    }

    /**
     * Register Operator
     * @OA\Post (
     *     path="/api/operators",
     *     tags={"Operators"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="name", type="string")
     *                 ),
     *                 example={
     *                     "name": "Entel",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="CREATED"
     *      ),
     *      @OA\Response(
     *      response=422,
     *      description="UNPROCESSABLE CONTENT",
     *      @OA\JsonContent(
     *          @OA\Property(property="messages", type="array", @OA\Items(type="string", example="The user_id field is required.")),
     *          @OA\Property(property="errors", type="string", example="Error Object"),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {

            $validate_data = $request->validate([
                'name' => 'required|string|max:10|unique:operators',
            ]);

            Operators::create($validate_data);

            return $this->sendResponse([], 'Operator created successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Operator
     * @OA\Get (
     *     path="/api/operators/{id}",
     *     tags={"Operators"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Anibal Gallardo"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for Customer"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $get_data = Operators::find($id);

            if (!$get_data) {
                return $this->sendResponse([], "Operator not found", 404);
            }

            $data = [
                "id" => $get_data->id,
                "name" => $get_data->name,
                "created_at" => $get_data->created_at,
            ];

            return $this->sendResponse($data, "Operators retrieve successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update Operator
     * @OA\Put (
     *     path="/api/operators/{id}",
     *     tags={"Operators"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "Anibal Gallardo"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     *      @OA\Response(
     *      response=422,
     *      description="UNPROCESSABLE CONTENT",
     *      @OA\JsonContent(
     *          @OA\Property(property="messages", type="array", @OA\Items(type="string", example="The name field is required.")),
     *          @OA\Property(property="errors", type="string", example="Error Object"),
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $validate_data = $request->validate([
                'name' => 'required|string|max:10|unique:operators',
            ]);

            $operator = Operators::find($id);

            $operator->update(array_filter($validate_data));
            return $this->sendResponse([], 'Operator created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Agency
     * @OA\Delete (
     *     path="/api/operators/{id}",
     *     tags={"Operators"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="NO CONTENT"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Operator not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $operator = Operators::find($id);

            if (!$operator) {
                return $this->sendResponse([], 'Operator not found', 404);
            }

            $operator->delete();

            return $this->sendResponse([], 'Operator deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    protected function sendResponse($data = [], $message, $status = 200)
    {
        return response()->json([
            "data" => $data,
            "message" => $message,
            "status" => $status
        ], $status);
    }
}
