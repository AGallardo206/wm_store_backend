<?php

namespace App\Http\Controllers;

use App\Models\CustomerRecord;
use Illuminate\Http\Request;


class CustomerRecordController extends Controller
{
    /**
     *  List of all Customer records
     * @OA\Get (
     *     path="/api/customers-records",
     *     tags={"CustomersRecords"},
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
     *                     @OA\Property(property="dni", type="string", example="01234567"),
     *                     @OA\Property(property="phone", type="string", example="01234578"),
     *                     @OA\Property(property="schedule_1", type="string", example="task 1"),
     *                     @OA\Property(property="schedule_2", type="string", example="task 2"),
     *                     @OA\Property(property="schedule_3", type="string", example="task 3"),
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="user_id", type="number", example=1),
     *                     @OA\Property(property="operator", type="number", example=1),
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

            $get_data = CustomerRecord::with(['user', 'operator', 'customer.phoneNumbers'])->paginate($per_page);

            if (!$get_data) {
                return $this->sendResponse([], 'No found Customer Records', 404);
            }
            $data = $get_data->map(function ($record) {
                return [
                    'id' => $record->id,
                    'name' => $record->customer->name ?? '',
                    'dni' => $record->customer->dni ?? '',
                    'phone' => $record->phone ?? '',
                    'schedule_1' => $record->schedule_1 ?? '',
                    'schedule_2' => $record->schedule_2 ?? '',
                    'schedule_3' => $record->schedule_3 ?? '',
                    'status' => $record->status == 0 ? false : true,
                    'user' => $record->user->name,
                    'operator' => $record->operator->name,
                    'created_at' => $record->created_at,
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
            return $this->sendResponse($response, 'Customers Records retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Customer Record
     * @OA\Post (
     *     path="/api/customers-records",
     *     tags={"CustomersRecords"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="user_id", type="number"),
     *                      @OA\Property(property="operator_id", type="number"),
     *                      @OA\Property(property="customer_id", type="string"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="schedule_1", type="string"),
     *                      @OA\Property(property="schedule_2", type="string"),
     *                      @OA\Property(property="schedule_3", type="string")
     *                 ),
     *                 example={
     *                     "user_id": 1,
     *                     "operator_id": 1,
     *                     "customer_id": 1,
     *                     "phone":"01234567",
     *                     "schedule_1":"Task 1",
     *                     "schedule_1":"Task 2",
     *                     "schedule_1":"Task 3",
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
                'user_id' => 'required|exists:users,id',
                'operator_id' => 'required|exists:operators,id',
                'customer_id' => 'required|exists:customers,id',
                'phone' => 'required|string|digits:9|unique:customer_records,phone',
                'schedule_1' => 'string|nullable',
                'schedule_2' => 'string|nullable',
                'schedule_3' => 'string|nullable',
            ]);

            CustomerRecord::create($validate_data);

            return $this->sendResponse([], 'Customer Record created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Show one Customer record
     * @OA\Get (
     *     path="/api/customers-records/{id}",
     *     tags={"CustomersRecords"},
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
     *              @OA\Property(property="name", type="string", example="Anibal Gallardo"),
     *              @OA\Property(property="dni", type="string", example="01234567"),
     *              @OA\Property(property="phone", type="string", example="01234578"),
     *              @OA\Property(property="schedule_1", type="string", example="task 1"),
     *              @OA\Property(property="schedule_2", type="string", example="task 2"),
     *              @OA\Property(property="schedule_3", type="string", example="task 3"),
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="user_id", type="number", example=1),
     *              @OA\Property(property="operator", type="number", example=1),
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
            $get_data = CustomerRecord::with(['user', 'operator', 'customer.phoneNumbers'])->where('user_id', $id)->first(); // Cargar relaciones
            if (!$get_data) {
                return $this->sendResponse([], 'Customer Record not found', 404);
            }
            $data = [
                'id' => $get_data->id,
                'name' => $get_data->customer->name ?? '',
                'dni' => $get_data->customer->dni ?? '',
                'phone' => $get_data->phone ?? '',
                'schedule_1' => $get_data->schedule_1 ?? '',
                'schedule_2' => $get_data->schedule_2 ?? '',
                'schedule_3' => $get_data->schedule_3 ?? '',
                'status' => $get_data->status == 0 ? false : true,
                'user' => $get_data->user->name,
                'operator' => $get_data->operator->name,
                'created_at' => $get_data->created_at,
            ];
            return $this->sendResponse($data, 'Customer Record retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }


    /**
     * Update Customer Record
     * @OA\Put (
     *     path="/api/customers-records/{id}",
     *     tags={"CustomersRecords"},
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
     *                          property="operator_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="customer_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="schedule_1",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="schedule_2",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="schedule_3",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="status",
     *                          type="boolean"
     *                      ),
     *                 ),
     *                 example={
     *                     "operator_id": 1,
     *                     "customer_id": 1,
     *                     "schedule_1": "Task 1",
     *                     "schedule_2": "Task 2",
     *                     "schedule_3": "Task 3",
     *                     "status": false
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
                'operator_id' => 'integer|exists:operators,id',
                'customer_id' => 'integer|exists:customers,id',
                'phone' => 'string|digits:9|unique:customer_records,phone',
                'schedule_1' => 'nullable|string',
                'schedule_2' => 'nullable|string',
                'schedule_3' => 'nullable|string',
                'status' => 'boolean',
            ]);
            $get_data = CustomerRecord::with('customer', 'operator')->where('customer_id', $id)->first();

            if (!$get_data) {
                return $this->sendResponse([], 'Customer Record not found', 404);
            }

            $get_data->update(array_filter($validate_data)); // Actualizar solo los campos que se pasaron
            return $this->sendResponse([], 'Customer Records updated successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Agency
     * @OA\Delete (
     *     path="/api/customers-records/{id}",
     *     tags={"CustomersRecords"},
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
     *              @OA\Property(property="message", type="string", example="Customer record not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $customer = CustomerRecord::find($id);
            if (!$customer) {
                return $this->sendResponse([], 'Customer record not found', 404);
            }
            $customer->delete();
            return $this->sendResponse([], 'Customer record deleted successfully', 200);
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

    protected function errorResponse($message, $status = 400)
    {
        return $this->sendResponse([], $message, $status);
    }
}
