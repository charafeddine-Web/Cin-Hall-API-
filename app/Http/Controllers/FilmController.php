<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Films",
 *     description="Gestion des films"
 * )
 */
class FilmController extends Controller
{
    protected $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/films",
     *     summary="Liste tous les films",
     *     tags={"Films"},
     *     @OA\Response(response=200, description="Liste des films"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index()
    {
        return response()->json($this->filmRepository->getAll(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/films",
     *     summary="Créer un nouveau film",
     *     tags={"Films"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titre", "description", "durée", "image", "age_minimum", "genre"},
     *             @OA\Property(property="titre", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="durée", type="integer"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="age_minimum", type="integer"),
     *             @OA\Property(property="genre", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Film créé"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'durée' => 'required|integer',
            'image' => 'required|string',
            'age_minimum' => 'required|integer',
            'genre' => 'required|string',
        ]);

        return response()->json($this->filmRepository->create($data), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/films/{id}",
     *     summary="Afficher un film spécifique",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Film trouvé"),
     *     @OA\Response(response=404, description="Film non trouvé")
     * )
     */
    public function show($id)
    {
        $film = $this->filmRepository->findById($id);
        if (!$film) {
            return response()->json(['message' => 'Film non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($film, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/films/{id}",
     *     summary="Mettre à jour un film",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="titre", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="durée", type="integer"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="age_minimum", type="integer"),
     *             @OA\Property(property="genre", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Film mis à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Film non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'durée' => 'sometimes|integer',
            'image' => 'sometimes|string',
            'age_minimum' => 'sometimes|integer',
            'genre' => 'sometimes|string',
        ]);

        $film = $this->filmRepository->update($id, $data);
        if (!$film) {
            return response()->json(['message' => 'Film non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($film, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/films/{id}",
     *     summary="Supprimer un film",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Film supprimé"),
     *     @OA\Response(response=404, description="Film non trouvé"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->filmRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Film non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Film supprimé'], Response::HTTP_OK);
    }
}
