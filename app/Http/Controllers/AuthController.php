<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Contracts\JWTSubject; // Add this import

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @OA\Info(title="Auth API", version="1.0")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Email ou mot de passe incorrect'], 401);
        }
        $user = auth()->user();
        return response()->json([
            'token JWT' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="User registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin", "spectateur"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,spectateur'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'token JWT' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout successful"),
     *     @OA\Response(response=400, description="No token provided"),
     *     @OA\Response(response=500, description="Logout failed")
     * )
     */
    public function logout()
    {
        try {
            if (!JWTAuth::getToken()) {
                return response()->json(['error' => 'Aucun token fourni'], 400);
            }

            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Échec de la déconnexion'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get current user details",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="User data retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me()
    {
        return response()->json(Auth::user());
    }
}
