<?php

namespace App\Http\Controllers;

use App\Models\SalesUser;
use Illuminate\Http\Request;

class SalesUserController extends Controller
{
    /**
     *  List of all Sales Users
     * @OA\Get (
     *     path="/api/sales-user",
     *     tags={"SalesUser"},
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
     *                     @OA\Property(property="agency_id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="WM_AGALLARDOG"),
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

            $get_data = SalesUser::paginate($per_page);

            if (!$get_data) {
                return $this->sendResponse([], 'No sales user found', 404);
            }

            $data = $get_data->map(function ($sales_user) {
                return [
                    'id' => $sales_user->id,
                    'agency_id' => $sales_user->agency_id,
                    'name' => $sales_user->name,
                    'created_at' => $sales_user->created_at,
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

            return $this->sendResponse($response, 'Sales user retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Sales User
     * @OA\Post (
     *     path="/api/sales-user",
     *     tags={"SalesUser"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="agency_id", type="number"),
     *                      @OA\Property(property="name", type="string"),
     *                 ),
     *                 example={
     *                     "agency_id": 1,
     *                     "name": "MW_AGALLARDOG"
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
                'agency_id' => 'required|integer',
                'name' => 'required|string|unique:sales_users,name',
            ]);

            SalesUser::create($validate_data);

            return $this->sendResponse([], 'Sales user created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Sale User
     * @OA\Get (
     *     path="/api/sales-user/{id}",
     *     tags={"SalesUser"},
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
     *              @OA\Property(property="agency_id", type="number", example="1"),
     *              @OA\Property(property="name", type="string", example="WM_AGALLARDOG"),
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
            $get_data = SalesUser::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Sales user not found', 404);
            }

            $data = [
                'id' => $get_data->id,
                'agency_id' => $get_data->agency_id,
                'name' => $get_data->name,
                'created_at' => $get_data->created_at,
            ];
            return $this->sendResponse($data, 'Sales user found successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 404);
        }
    }

    /**
     * Update Sales User
     * @OA\Put (
     *     path="/api/sales-user/{id}",
     *     tags={"SalesUser"},
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
     *                          property="agency_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "agency_id": "2",
     *                     "name": "MW_GALLARDOAG"
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
            $salesUser = SalesUser::find($id);

            $validate_data = $request->validate([
                'agency_id' => 'integer',
                'name' => 'string|unique:sales_users,name,', // Ignore the current record in unique check
            ]);

            $salesUser->update($validate_data);

            return $this->sendResponse([], 'Sales user updated successfully', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendResponse([], 'Sales user not found', 404);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Sale User
     * @OA\Delete (
     *     path="/api/sales-user/{id}",
     *     tags={"SalesUser"},
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
     *              @OA\Property(property="message", type="string", example="Sale User not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $get_data = SalesUser::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Sales user not found', 404);
            };

            $get_data->delete();
            return $this->sendResponse([], 'Sales user delete successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    protected function sendResponse($data, $message, $status = 200)
    {
        return response()->json([
            "data" => $data,
            "message" => $message,
            "status" => $status
        ], $status);
    }
}
