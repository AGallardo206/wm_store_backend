<?php

namespace App\Http\Controllers;

use App\Models\SalesType;
use Illuminate\Http\Request;

class SalesTypeController extends Controller
{
    /**
     *  List of all Sales Types
     * @OA\Get (
     *     path="/api/sales-type",
     *     tags={"SalesType"},
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
     *                     @OA\Property(property="name", type="string", example="Equipos"),
     *                     @OA\Property(property="description", type="string", example="Venta de equipos"),
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
            $per_page = $request->query('per_page', 20);

            $get_data = SalesType::orderBy("name", "ASC")->paginate($per_page);


            if (!$get_data) {
                return $this->sendResponse([], 'No sales types found', 404);
            }

            $data = $get_data->map(function ($sales_type) {
                return [
                    'id' => $sales_type->id,
                    'name' => $sales_type->name,
                    'description' => $sales_type->description != null ? $sales_type->description : "",
                    'created_at' => $sales_type->created_at,
                ];
            });

            $response = [
                "data" => $data->toArray(),
                "links" => $get_data->links(),
                "meta" => [
                    "current_page" => $get_data->currentPage(),
                    "total" => $get_data->total(),
                    "per_page" => $per_page
                ],
            ];

            return $this->sendResponse($response, 'Sales types retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Sale Type
     * @OA\Post (
     *     path="/api/sales-type",
     *     tags={"SalesType"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                 ),
     *                 example={
     *                     "name": "Equipos",
     *                     "description": "Ventas de equipos"
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
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            SalesType::create($validatedData);

            return $this->sendResponse([], 'Sales type created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Sale Type
     * @OA\Get (
     *     path="/api/sales-type/{id}",
     *     tags={"SalesType"},
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
     *              @OA\Property(property="name", type="string", example="Equipos"),
     *              @OA\Property(property="description", type="string", example="Venta de equipos"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for Type sale"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $sales_type = SalesType::find($id);

            if (!$sales_type) {
                return $this->sendResponse([], 'Sales type not found', 404);
            }

            $data = [
                'id' => $sales_type->id,
                'name' => $sales_type->name,
                'description' => $sales_type->description != null ? $sales_type->description : "",
                'created_at' => $sales_type->created_at,
            ];
            return $this->sendResponse($data, 'Sales type retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update Sales
     * @OA\Put (
     *     path="/api/sales-type/{id}",
     *     tags={"SalesType"},
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
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "VR",
     *                     "description": "Linea Nueva",
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
            $sales_type = SalesType::find($id);

            if (!$sales_type) {
                return $this->sendResponse([], 'Sales type not found', 404);
            }

            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            $sales_type->update($validatedData);

            return $this->sendResponse([], 'Sales type updated successfully');
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Sale Type
     * @OA\Delete (
     *     path="/api/sales-type/{id}",
     *     tags={"SalesType"},
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
     *              @OA\Property(property="message", type="string", example="Sale Type not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $sales_type = SalesType::find($id);

            if (!$sales_type) {
                return $this->sendResponse([], 'Sales type not found', 404);
            }

            $sales_type->delete();

            return $this->sendResponse([], 'Sales type deleted successfully', 200);
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
