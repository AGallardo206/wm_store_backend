<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     *  List of all Customers
     * @OA\Get (
     *     path="/api/customers",
     *     tags={"Customers"},
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
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="agency_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="dni",
     *                         type="string",
     *                         example="012345678"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Anibal Gallardo"
     *                     ),
     *                     @OA\Property(property="phone_numbers", type="array", @OA\Items(
     *                          @OA\Property(property="phone", type="string", example="012345678"),
     *                          @OA\Property(property="operator_id", type="number", example=1),
     *                          @OA\Property(property="operator", type="string", example="Entel")
     *                      )),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-02-23T12:33:45.000000Z"
     *                     )
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

            $get_data = Customer::with('phoneNumbers.operator')->paginate($per_page);

            if ($get_data->isEmpty()) {
                return $this->sendResponse([], 'No found customers', 404);
            }
            $data = $get_data->map(function ($customer) {
                return [
                    "id" => $customer->id,
                    "agency_id" => $customer->agency_id,
                    "dni" => $customer->dni,
                    "name" => $customer->name,
                    "phone_numbers" => $customer->phoneNumbers->map(function ($phoneNumber) {
                        return [
                            'phone' => $phoneNumber->phone,
                            'operator_id' => $phoneNumber->operator_id,
                            'operator' => $phoneNumber->operator->name,
                        ];
                    })->toArray(),
                    "created_at" => $customer->created_at,
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
            return $this->sendResponse($response, "Customers retrieved successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Customer
     * @OA\Post (
     *     path="/api/customers",
     *     tags={"Customers"},
     *     security={{"passport": {}}},
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
     *                      ),
     *                      @OA\Property(
     *                          property="dni",
     *                          type="string"
     *                      ),
     *                     @OA\Property(property="phone_numbers", type="array", @OA\Items(
     *                          @OA\Property(property="phone", type="string", example="012345678"),
     *                          @OA\Property(property="operator_id", type="number", example=1),
     *                          @OA\Property(property="operator", type="string", example="Entel")
     *                      )),
     *                      @OA\Property(
     *                          property="operator_id",
     *                          type="number"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"Anibal Gallardo",
     *                     "address":"Tacna Peru",
     *                     "phone":"906393152",
     *                     "email":"anibal.gg.206@gmail.com"
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
            $request->validate([
                'agency_id' => 'required|integer',
                'name' => 'required|max:255',
                'dni' => 'required|max:8',
                'phone' => 'string:digits:9|unique:phones_numbers,phone',
                'operator_id' => 'integer|exists:operators,id',
            ]);
            $customer = Customer::create($request->only('agency_id', 'name', 'dni'));
            if ($request->has('phone')) {
                $customer->phoneNumbers()->create([
                    'phone' => $request->phone,
                    'operator_id' => $request->operator_id, // Puede ser null si no se proporciona
                ]);
            }
            return $this->sendResponse([], "Customer created successfully", 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one customer
     * @OA\Get (
     *     path="/api/custormers/{id}",
     *     tags={"Customers"},
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
     *              @OA\Property(property="agency_id", type="string", example="1"),
     *              @OA\Property(property="dni", type="string", example="01234567"),
     *              @OA\Property(property="phone_numbers", type="array", @OA\Items(
     *                   @OA\Property(property="phone", type="string", example="012345678"),
     *                   @OA\Property(property="operator_id", type="number", example=1),
     *                   @OA\Property(property="operator", type="string", example="Entel")
     *              )),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z")
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
            $get_data = Customer::with('phoneNumbers.operator')->where('dni', $id)->first();

            if (!$get_data) {
                return $this->sendResponse([], 'Customer not found', 404);
            }

            $data = [
                "id" => $get_data->id,
                "agency_id" => $get_data->agency_id,
                "dni" => $get_data->dni,
                "name" => $get_data->name,
                "phone_numbers" => $get_data->phoneNumbers->map(function ($phoneNumber) {
                    return [
                        'phone' => $phoneNumber->phone,
                        'operator_id' => $phoneNumber->operator_id,
                        'operator' => $phoneNumber->operator->name,
                    ];
                })->toArray(),
                "created_at" => $get_data->created_at,
            ];
            return $this->sendResponse($data, "Customer retrieved successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update agency
     * @OA\Put (
     *     path="/api/customers/{id}",
     *     tags={"Customers"},
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
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="dni",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="operator_id",
     *                          type="number"
     *                      )
     *                 ),
     *                 example={
     *                     "agency_id": 1,
     *                     "name": "Anibal Gallardo Gonzalez",
     *                     "dni": "01234567",
     *                     "operator_id": 1
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
                'agency_id' => 'integer', // Allow null values
                'name' => 'max:255', // Allow null values
                'dni' => 'string|max:8', // Ensure this is a string with a max length
                'operator_id' => 'integer|exists:operators,id', // Validation for the operator
            ]);
            $customer = Customer::where('dni', $id)->first();
            $customer->update(array_filter($validate_data));
            return $this->sendResponse([], "Customer updated successfully", 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendResponse([], 'Customer not found', 404);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Customer
     * @OA\Delete (
     *     path="/api/customers/{id}",
     *     tags={"Customers"},
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
     *              @OA\Property(property="message", type="string", example="Customer not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->phoneNumbers()->delete(); // Delete related phone numbers
            $customer->delete(); // Delete the customer
            return $this->sendResponse([], "Customer deleted successfully", 200);
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
