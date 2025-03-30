<?php

namespace App\Http\Controllers;

use App\Services\FilmService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Films", description="Endpoints pour gérer les films")
 */
class FilmController extends Controller
{
    protected $filmService;

    public function __construct(FilmService $filmService)
    {
        $this->filmService = $filmService;
    }

    /**
     * @OA\Get(
     *     path="/films",
     *     summary="Récupérer la liste des films",
     *     tags={"Films"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des films récupérée avec succès",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function index()
    {
        return response()->json($this->filmService->getAll());
    }

    /**
     * @OA\Get(
     *     path="/films/{id}",
     *     summary="Récupérer un film par ID",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du film",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Film récupéré avec succès",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function show($id)
    {
        return response()->json($this->filmService->get($id));
    }

    /**
     * @OA\Post(
     *     path="/films",
     *     summary="Créer un nouveau film",
     *     tags={"Films"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Film créé avec succès",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function store(Request $request)
    {
        return response()->json($this->filmService->create($request->all()), 201);
    }

    /**
     * @OA\Put(
     *     path="/films/{id}",
     *     summary="Mettre à jour un film",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du film à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Film mis à jour avec succès",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        return response()->json($this->filmService->update($id, $request->all()));
    }

    /**
     * @OA\Delete(
     *     path="/films/{id}",
     *     summary="Supprimer un film",
     *     tags={"Films"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du film à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Film supprimé avec succès",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function destroy($id)
    {
        return response()->json($this->filmService->delete($id));
    }
}
