<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepositoryInterface;

class AuthController extends Controller
{
    private $Userrepository;
    public function __construct(UserRepositoryInterface $UserRepository)
    {
        return $this->Userrepository = $UserRepository;
    }

    /**
* @OA\Post(
*     path="/api/register",
*     summary="Créer un nouvel utilisateur avec JWT",
*     tags={"Authentification"},
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name","email","password","password_confirmation"},
*             @OA\Property(property="name", type="string", example="John Doe"),
*             @OA\Property(property="email", type="string", example="johndoe@example.com"),
*             @OA\Property(property="password", type="string", example="password123"),
*             @OA\Property(property="password_confirmation", type="string", example="password123")
*         )
*     ),
*     @OA\Response(response=201, description="Utilisateur créé avec succès"),
*     @OA\Response(response=422, description="Validation échouée")
* )
*/

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'FullName' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
        }

        $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
        'message' => 'Utilisateur créé avec succès',
        'user' => $user,
        'token' => $token
        ], 201);
}

/**
* @OA\Post(
*     path="/api/login",
*     summary="Authentifier un utilisateur et générer un token JWT",
*     tags={"Authentification"},
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"email","password"},
*             @OA\Property(property="email", type="string", example="johndoe@example.com"),
*             @OA\Property(property="password", type="string", example="password123")
*         )
*     ),
*     @OA\Response(response=200, description="Connexion réussie"),
*     @OA\Response(response=401, description="Identifiants incorrects")
* )
*/
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
        $user = $this->Userrepository->finbByEmail($credentials['email']);

        try {
            if (!$user | !$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Erreur interne du serveur'], 500);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => auth()->user(),
            'token' => $token
        ], 200);
}

/**
* @OA\Post(
*     path="/api/logout",
*     summary="Déconnexion de l'utilisateur",
*     tags={"Authentification"},
*     security={{"bearerAuth":{}}}
* )
*/
public function logout(Request $request)
{
    try {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Déconnexion réussie'], 200);
    } catch (JWTException $e) {
        return response()->json(['message' => 'Erreur lors de la déconnexion'], 500);
    }
}

/**
* @OA\Get(
*     path="/api/user",
*     summary="Récupérer l'utilisateur connecté",
*     tags={"Authentification"},
*     security={{"bearerAuth":{}}}
* )
*/
public function user()
{
    return response()->json(['user' => auth()->user()], 200);
}
}
