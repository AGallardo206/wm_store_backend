<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *             title="W&M Store",
 *             version="1.0",
 *             description="Listado de URI's of Agency API"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="passport",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token in the format **Bearer {token}**"
 * )
 * @OA\Server(url="http://localhost:85")
 */

class AgencyController extends Controller
{
    /**
     *  List of all agencies
     * @OA\Get (
     *     path="/api/agencies",
     *     security={{"passport": {}}},
     *     tags={"Agencies"},
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
     *                         property="name",
     *                         type="string",
     *                         example="Jhon Doe"
     *                     ),
     *                     @OA\Property(
     *                         property="address",
     *                         type="string",
     *                         example="My home"
     *                     ),
     *                     @OA\Property(
     *                         property="phone",
     *                         type="string",
     *                         example="01234578"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="anibal.gg.206@gmail.com"
     *                     ),
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
    public function index()
    {
        try {
            $get_data = Agency::all();
            if (!$get_data) {
                return $this->sendResponse([], 'No agency found', 404);
            }
            $data = $get_data->map(function ($get_data) {
                return [
                    'id' => $get_data->id,
                    'name' => $get_data->name,
                    'address' => $get_data->address != null ? $get_data->address : "",
                    'phone' => $get_data->phone != null ? $get_data->phone : "",
                    'email' => $get_data->email != null ? $get_data->email : "",
                    'created_at' => $get_data->created_at,
                ];
            });
            return $this->sendResponse($data, 'Agencies retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Register Agency
     * @OA\Post (
     *     path="/api/agencies",
     *     tags={"Agencies"},
     *     security={{"passport": {}}},
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
     *                          property="address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"TEX Tacna Sur",
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
            $validateData = $request->validate([
                'name' => 'required|string|max:100',
                'address' => 'required|string|max:100',
                'phone' => 'nullable|digits:9',
                'email' => 'nullable|email|unique:agencies,email',
            ]);
            Agency::create($validateData);
            return $this->sendResponse([], 'Agency created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Show one Agency
     * @OA\Get (
     *     path="/api/agencies/{id}",
     *     tags={"Agencies"},
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
     *              @OA\Property(property="address", type="string", example="Peru"),
     *              @OA\Property(property="phone", type="string", example="01234567"),
     *              @OA\Property(property="email", type="string", example="anibal.gg.206@gmail.com"),
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
            $get_data = Agency::all()->find($id);
            if (!$get_data) {
                return $this->sendResponse([], 'No agency found', 404);
            }
            $data = [
                'id' => $get_data->id,
                'name' => $get_data->name,
                'address' => $get_data->address != null ? $get_data->address : "",
                'phone' => $get_data->phone != null ? $get_data->phone : "",
                'email' => $get_data->email != null ? $get_data->email : "",
                'created_at' => $get_data->created_at,
            ];
            return $this->sendResponse($data, 'Agency retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Update agency
     * @OA\Put (
     *     path="/api/agencies/{id}",
     *     tags={"Agencies"},
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
     *                          property="address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "Anibal Gallardo Gonzalez",
     *                     "address": "Peru Tacna",
     *                     "phone": "906393152",
     *                     "email": "anibal.gg.206@gmai.com"
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
            $get_data = Agency::find($id);
            if (!$get_data) {
                return $this->sendResponse([], 'Agency not found', 404);
            }
            $validateData = $request->validate([
                'name' => 'string|max:100',
                'address' => 'string|max:100',
                'phone' => 'digits:9',
                'email' => 'email|unique:agencies,email',
            ]);
            $get_data->update(array_filter($validateData));
            return $this->sendResponse([], 'Agency updated successfully', 200);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete Agency
     * @OA\Delete (
     *     path="/api/agencies/{id}",
     *     tags={"Agencies"},
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
     *              @OA\Property(property="message", type="string", example="Agency not found"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        try {
            $get_data = Agency::find($id);
            if (!$get_data) {
                return $this->sendResponse([], 'Agency not found', 404);
            }
            $get_data->delete();
            return $this->sendResponse([], 'Agency deleted successfully', 200);
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
