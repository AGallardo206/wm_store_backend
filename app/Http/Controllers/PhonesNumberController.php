<?php

namespace App\Http\Controllers;

use App\Models\PhonesNumber;
use Illuminate\Http\Request;

class PhonesNumberController extends Controller
{
    /**
     *  List of all Phones numbers available
     * @OA\Get (
     *     path="/api/phones",
     *     tags={"Phones"},
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
     *                     @OA\Property(property="customer_id", type="string", example="Anibal Gallardo"),
     *                     @OA\Property(property="operators_id", type="string", example="01234567"),
     *                     @OA\Property(property="phone", type="string", example="01234578"),
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
            $get_data = PhonesNumber::all();

            if ($get_data->isEmpty()) {
                return $this->sendResponse([], 'No found Phones', 404);
            }

            $data = $get_data->map(function ($get_data) {
                return [
                    "id" => $get_data->id,
                    "customer_id" => $get_data->customer_id,
                    "operators_id" => $get_data->operator_id != null ? $get_data->operator_id : '',
                    "phone" => $get_data->phone != null ? $get_data->phone : '',
                    "created_at" => $get_data->created_at,
                ];
            });
            return $this->sendResponse($data, "Phones retrieve successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Phones
     * @OA\Post (
     *     path="/api/phones",
     *     tags={"Phones"},
     *     security={{"passport": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="phone", type="number"),
     *                      @OA\Property(property="customer_id", type="number"),
     *                      @OA\Property(property="operator_id", type="number"),
     *                      @OA\Property(property="equip", type="string"),
     *                      @OA\Property(property="imei", type="string")
     *                 ),
     *                 example={
     *                     "phone": 1,
     *                     "customer_id": 1,
     *                     "operator_id": 1,
     *                     "equip":"01234567",
     *                     "imei":"01234567"
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
                'phone' => 'required|string|unique:phones_numbers,phone|max:9',
                'customer_id' => 'required|integer',
                'operator_id' => 'required|integer',
                'equip' => 'string|max:255',
                'imei' => 'string|max:15',
            ]);

            PhonesNumber::create($validate_data);

            return $this->sendResponse([], 'Register Phone successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Operator
     * @OA\Get (
     *     path="/api/phone/{id}",
     *     tags={"Phones"},
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
     *              @OA\Property(property="customer_id", type="number", example= "1"),
     *              @OA\Property(property="operator_id", type="number", example= "1"),
     *              @OA\Property(property="phone", type="string", example="906393152"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for Operator"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $get_data = PhonesNumber::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Phone not found', 404);
            };

            $data = [
                "id" => $get_data->id,
                "customer_id" => $get_data->customer_id,
                "operator_id" => $get_data->operator_id != null ? $get_data->operator_id : '',
                "phone" => $get_data->phone != null ? $get_data->phone : '',
                "created_at" => $get_data->created_at,
            ];

            return $this->sendResponse($data, 'Phone found successfully', 200);
        } catch (\Exception $e) {
            return $this->seesponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update Phones
     * @OA\Put (
     *     path="/api/phones/{id}",
     *     tags={"Phones"},
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
     *                          property="customer_id",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="operator_id",
     *                          type="number"
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
     *                 ),
     *                 example={
     *                     "customer_id": 1,
     *                     "operator_id": 1,
     *                     "phone": "906393152",
     *                     "equip": "Xiaomi Note 11",
     *                     "imei": "012345678912345"
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
            // Find the phone number by ID or fail
            $get_data = PhonesNumber::find($id);
            if (!$get_data) {
                return $this->sendResponse([], 'Phone not found', 404);
            }

            // Validate the incoming request data
            $validate_data = $request->validate([
                'customer_id' => 'integer',
                'operator_id' => 'integer',
                'phone' => 'string|unique:phones_numbers,phone,', // Ignore current record
                'equip' => 'string|max:255',
                'ime' => 'string|max:15',
            ]);

            // Update the phone number with the validated data
            $get_data->update(array_filter($validate_data));

            // Return a success response
            return $this->sendResponse([], "Phone number updated successfully", 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Operator
     * @OA\Delete (
     *     path="/api/phones/{id}",
     *     tags={"Phones"},
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
     *              @OA\Property(property="message", type="string", example="Operator record not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $get_data = PhonesNumber::find($id);

            if (!$get_data) {
                return $this->sendResponse([], 'Phone not found', 404);
            };

            $get_data->delete();
            return $this->sendResponse([], 'Phone delete successfully', 200);
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
