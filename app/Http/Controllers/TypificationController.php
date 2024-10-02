<?php

namespace App\Http\Controllers;

use App\Models\Typification;
use Illuminate\Http\Request;

class TypificationController extends Controller
{
    /**
     *  List of all Typifications
     * @OA\Get (
     *     path="/api/typifications",
     *     tags={"Typifications"},
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
     *                     @OA\Property(property="name", type="string", example="Satisfecho"),
     *                     @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {

        try {
            $per_page = $request->query('per_page', 10);

            $get_data = Typification::paginate($per_page);

            if (!$get_data) {
                return $this->sendResponse([], 'No Typification found', 404);
            }

            $data = $get_data->map(function ($get_data) {
                return [
                    'id' => $get_data->id,
                    'name' => $get_data->name,
                    'created_at' => $get_data->created_at,
                ];
            });

            $response = [
                "data" => $data->toArray(),
                "links" => $get_data->links(),
                "meta" => [
                    "current_page" => $get_data->currentPage(),
                    "total" => $get_data->total(),
                    "per_page" => $per_page,
                ],
            ];


            return $this->sendResponse($response, 'Typification retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Typifications
     * @OA\Post (
     *     path="/api/typifications",
     *     tags={"Typifications"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="name", type="string"),
     *                 ),
     *                 example={
     *                     "name": "Disgustado"
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
     *          @OA\Property(property="messages", type="array", @OA\Items(type="string", example="The name field is required.")),
     *          @OA\Property(property="errors", type="string", example="Error Object"),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validate_data = $request->validate([
                'name' => 'required|string|max:100',
            ]);

            Typification::create($validate_data);

            return $this->sendResponse([], 'Sales type created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Typification
     * @OA\Get (
     *     path="/api/typifications/{id}",
     *     tags={"Typifications"},
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
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="name", type="string", example="Satisfecho"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for Sales User"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $get_data = Typification::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Typification not found', 404);
            }

            $data = [
                'id' => $get_data->id,
                'name' => $get_data->name,
                'created_at' => $get_data->created_at,
            ];

            return $this->sendResponse($data, 'Typification retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update Typifications
     * @OA\Put (
     *     path="/api/typifications/{id}",
     *     tags={"Typifications"},
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
     *                     "name": "Satisfecho"
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
            $get_data = Typification::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Typification not found', 404);
            }

            $validated_data = $request->validate([
                'name' => 'nullable|string|max:255',
            ]);

            $get_data->update($validated_data);

            return $this->sendResponse([], 'Typification updated successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Typification
     * @OA\Delete (
     *     path="/api/typifications/{id}",
     *     tags={"Typifications"},
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
     *              @OA\Property(property="message", type="string", example="Typification not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $get_data = Typification::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Typification not found', 404);
            }

            $get_data->delete();

            return $this->sendResponse([], 'Typification deleted successfully');
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Helper function to standardize response structure.
     */
    protected function sendResponse($data, $message, $status = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }
}
