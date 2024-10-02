<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Register",
 *     description="Operations related to Register"
 * )
 */
class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Register"},
     *     summary="Register a new user",
     *     description="Creates a new user and returns the access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","c_password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="c_password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", @OA\Property(property="access_token", type="string", example="token_value"), @OA\Property(property="name", type="string", example="John Doe")),
     *             @OA\Property(property="message", type="string", example="Registro de Usuario Exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de Validación"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return $this->sendResponse([], $validator->errors(), 400);
        }

        try {
            $input = $request->only(['name', 'email', 'password']);
            $input['password'] = bcrypt($request->password);
            $user = User::create($input);

            $success = [
                'access_token' => $user->createToken('MyApp')->accessToken,
                'name' => $user->name,
            ];

            return $this->sendResponse([], 'Registro de Usuario Exitoso');
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Register"},
     *     summary="Login an existing user",
     *     description="Authenticates a user and returns the access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", @OA\Property(property="access_token", type="string", example="token_value"), @OA\Property(property="user", type="string", example="John Doe"), @OA\Property(property="login", type="boolean", example=true)),
     *             @OA\Property(property="message", type="string", example="Inicio de Sesión Exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No Autorizado"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $success = [
                    'access_token' => $user->createToken('MyApp')->accessToken,
                    'user' => $user->name,
                    'login' => true,
                ];
                return $this->sendResponse($success, 'Login Successful');
            }

            return $this->sendResponse([], 'Invalid credentials', 401);
        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), 500, $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Register"},
     *     security={{"passport": {}}},
     *     summary="Logout the authenticated user",
     *     description="Logs out the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Logout exitoso")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendResponse([], 'Unauthorized', 401);
        }

        $user->tokens()->delete();
        return $this->sendResponse([], 'Logout successfully', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Register"},
     *     security={{"passport": {}}},
     *     summary="Get authenticated user data",
     *     description="Returns the data of the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="name", type="string", example="John Doe")),
     *             @OA\Property(property="message", type="string", example="Datos del usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No Autorizado"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function user(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendResponse([], 'Unauthorized', 401);
        }

        $data = [
            'id' => $user->id,
            'agency_id' => $user->agency_id,
            'name' => $user->name,
        ];

        return $this->sendResponse($data, 'Datos del usuario');
    }

    /**
     * Método para enviar respuestas de éxito.
     *
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($data, $message, $status = 200, $errors)
    {
        return response()->json([
            "data" => $data,
            "message" => $message,
            "status" => $status,
            "errors" => $errors
        ], $status);
    }
}
