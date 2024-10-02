<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     *  List of all Sales
     * @OA\Get (
     *     path="/api/sales",
     *     tags={"Sales"},
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
     *                     @OA\Property(property="agency", type="number", example="1"),
     *                     @OA\Property(property="consultant", type="string", example="Milagros Garcia"),
     *                     @OA\Property(property="sales_user", type="string", example="WM_MGARCIAG"),
     *                     @OA\Property(property="customer", type="string", example="Anibal Gallardo"),
     *                     @OA\Property(property="dni", type="string", example="0123457"),
     *                     @OA\Property(property="address", type="string", example="Tacna"),
     *                     @OA\Property(property="phone", type="string", example="01234578"),
     *                     @OA\Property(property="origin", type="string", example="Postpago"),
     *                     @OA\Property(property="typification", type="string", example="Satisfecho"),
     *                     @OA\Property(property="sales_order", type="string", example="Equipos"),
     *                     @OA\Property(property="notes", type="string", example="Task"),
     *                     @OA\Property(property="equip", type="string", example="Xiaomi Note 11"),
     *                     @OA\Property(property="imei", type="string", example="012345678912345"),
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

            $get_data = Sales::with(['user', 'salesUser.agency', 'customer.phoneNumbers', 'typification'])->paginate($per_page);

            if (!$get_data) {
                return $this->sendResponse([], "No found sales", 404);
            }

            $data = $get_data->map(function ($sales) {
                return [
                    "id" => $sales->id,
                    "agency" => $sales->salesUser && $sales->salesUser->agency->name ? $sales->salesUser->agency->name : "",
                    "consultant" => $sales->user ? $sales->user->name : "",
                    "sales_user" => $sales->salesUser ? $sales->salesUser->name : "",
                    "customer" => $sales->customer ? $sales->customer->name : "",
                    "dni" => $sales->customer ? $sales->customer->dni : "",
                    "address" => $sales->customer->address ?? "",
                    "origin" => $sales->origin,
                    "typification" => $sales->typification ? $sales->typification->name : "",
                    "sales_order" => $sales->sales_order,
                    "phone" => $sales->phone ?? "",
                    "equip" => $sales->equip ?? "",
                    "imei" => $sales->imei ?? "",
                    "notes" => $sales->notes ?? "",
                    "created_at" => $sales->created_at,
                ];
            });

            $response = [
                "data" => $data->toArray(),
                "links" => $get_data->getUrlRange(1, $get_data->lastPage()),
                "meta" => [
                    "current_page" => $get_data->currentPage(),
                    "total" => $get_data->total(),
                    "per_page" => $per_page,
                    "last_page" => $get_data->lastPage(),
                ],
            ];

            return $this->sendResponse($response, "Sales retrieved successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Sale
     * @OA\Post (
     *     path="/api/sales",
     *     tags={"Sales"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="user_id", type="number"),
     *                      @OA\Property(property="sales_user_id", type="number"),
     *                      @OA\Property(property="customer_id", type="number"),
     *                      @OA\Property(property="operator_id", type="number"),
     *                      @OA\Property(property="sales_type_id", type="number"),
     *                      @OA\Property(property="typification_id", type="number"),
     *                      @OA\Property(property="origin", type="string"),
     *                      @OA\Property(property="sales_order", type="string"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="equip", type="string"),
     *                      @OA\Property(property="imei", type="string"),
     *                      @OA\Property(property="notes", type="string")
     *                 ),
     *                 example={
     *                     "user_id": 1,
     *                     "sales_user_id": 1,
     *                     "customer_id": 1,
     *                     "operator_id": 1,
     *                     "sales_type_id": 1,
     *                     "typification_id": 1,
     *                     "origin":"Postpago",
     *                     "sales_order":"0123456789",
     *                     "phone":"906393752",
     *                     "equip":"Xiaomi Note 13",
     *                     "imei":"012345678912345",
     *                     "notes":"Tasks"
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
                'user_id' => 'required|integer|exists:users,id',
                'sales_user_id' => 'required|integer|exists:sales_users,id',
                'customer_id' => 'required|integer|exists:customers,id',
                'operator_id' => 'required|integer|exists:operators,id',
                'sales_type_id' => 'required|integer|exists:sales_types,id',
                'typification_id' => 'required|integer|exists:typifications,id',
                'origin' => 'required|string',
                'sales_order' => 'required|string|max:9|unique:sales,sales_order',
                'phone' => 'required|string|digits:9',
                'equip' => 'nullable|string',
                'imei' => 'nullable|string|digits:15',
                'notes' => 'nullable|string',
            ]);

            Sales::create($validate_data);

            return $this->sendResponse([], 'Ventas creadas exitosamente', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Sale
     * @OA\Get (
     *     path="/api/sales/{id}",
     *     tags={"Sales"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="sales_order",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="agency", type="number", example="1"),
     *              @OA\Property(property="consultant", type="string", example="Milagros Garcia"),
     *              @OA\Property(property="sales_user", type="string", example="WM_MGARCIAG"),
     *              @OA\Property(property="customer", type="string", example="Anibal Gallardo"),
     *              @OA\Property(property="dni", type="string", example="0123457"),
     *              @OA\Property(property="address", type="string", example="Tacna"),
     *              @OA\Property(property="phone", type="string", example="01234578"),
     *              @OA\Property(property="origin", type="string", example="Postpago"),
     *              @OA\Property(property="typification", type="string", example="Satisfecho"),
     *              @OA\Property(property="sales_order", type="string", example="Equipos"),
     *              @OA\Property(property="notes", type="string", example="Task"),
     *              @OA\Property(property="equip", type="string", example="Xiaomi Note 11"),
     *              @OA\Property(property="imei", type="string", example="012345678912345"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for Sales"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {

        try {
            $sales = Sales::with(['user', 'salesUser.agency', 'customer.phoneNumbers', 'typification'])->where('sales_order', $id)->first();

            if (!$sales) {
                return response()->json(['message' => 'Sale not found'], 404);
            }

            $data = [
                "id" => $sales->id,
                "agency" => $sales->salesUser && $sales->salesUser->agency->name ? $sales->salesUser->agency->name : "",
                "consultant" => $sales->user ? $sales->user->name : "",
                "sales_user" => $sales->salesUser ? $sales->salesUser->name : "",
                "customer" => $sales->customer ? $sales->customer->name : "",
                "typification" => $sales->typification ? $sales->typification->name : "",
                "dni" => $sales->customer ? $sales->customer->dni : "",
                "address" => $sales->customer->address ?? "",
                "phone" => $sales->phone ?? "",
                "origin" => $sales->origin,
                "equip" => $sales->equip ?? "",
                "imei" => $sales->imei ?? "",
                "sales_order" => $sales->sales_order ?? "",
                "notes" => $sales->notes ?? "",
                "created_at" => $sales->created_at,
            ];

            return $this->sendResponse($data, "Sale found successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse($e->getMessage(), $e->getMessage(), 500);
        }
    }

    /**
     * Update Sales
     * @OA\Put (
     *     path="/api/sales/{id}",
     *     tags={"Sales"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="sales_order",
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
     *                          property="user_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="sales_user_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="customer_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="typification_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="operator_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="origin",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="sales_order",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="equip",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="imei",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="notes",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "user_id": 1,
     *                     "sales_user_id": 1,
     *                     "customer_id": 1,
     *                     "typification_id": 1,
     *                     "operator_id": 1,
     *                     "origin": "Postpago",
     *                     "sales_order": "012345678",
     *                     "phone": "906393152",
     *                     "equip": "Xiaomi Note 11",
     *                     "imei": "012345678912345",
     *                     "notes": "Notes"
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
     *          @OA\Property(property="messages", type="array", @OA\Items(type="string", example="The user_id field is required.")),
     *          @OA\Property(property="errors", type="string", example="Error Object"),
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {

        try {
            $validate_data = $request->validate([
                'user_id' => 'integer|exists:users,id',
                'sales_user_id' => 'integer|exists:sales_users,id',
                'customer_id' => 'integer|exists:customers,id',
                'typification_id' => 'integer|exists:typifications,id',
                'operator_id' => 'integer|exists:operator,id',
                'origin' => 'string',
                'sales_order' => 'string|digits:9|unique:sales,sales_order',
                'phone' => 'string|digits:9',
                'equip' => 'string',
                'imei' => 'string|digits:15',
                'notes' => 'string',
            ]);

            $sale = Sales::where('sales_order', $id)->first();
            if (!$sale) {
                return $this->sendResponse([], 'Sale not found', 404);
            }

            $sale->update($validate_data);

            return $this->sendResponse([], 'Sales update successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Sale
     * @OA\Delete (
     *     path="/api/sales/{id}",
     *     tags={"Sales"},
     *     security={{"passport": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="sales_order",
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
     *              @OA\Property(property="message", type="string", example="Sale not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $sale = Sales::where('sales_order', $id)->first();

            if (!$sale) {
                return $this->sendResponse([], 'Sale not found', 404);
            }

            $sale->delete();
            return $this->sendResponse([], 'Sale deleted successfully', 200);
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
