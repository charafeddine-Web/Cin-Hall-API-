<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SalleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Salles",
 *     description="Gestion des salles"
 * )
 */
class SalleController extends Controller
{
    protected $salleRepository;

    public function __construct(SalleRepositoryInterface $salleRepository)
    {
        $this->salleRepository = $salleRepository;

        $this->middleware('permission:view_salles')->only(['index', 'show']);
        $this->middleware('permission:create_salle')->only(['store']);
        $this->middleware('permission:edit_salle')->only(['update']);
        $this->middleware('permission:delete_salle')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/salles",
     *     summary="Liste toutes les salles",
     *     tags={"Salles"},
     *     @OA\Response(response=200, description="Liste des salles"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index()
    {
        return response()->json($this->salleRepository->all(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/salles",
     *     summary="Créer une nouvelle salle",
     *     tags={"Salles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "capacite", "type"},
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="capacite", type="integer"),
     *             @OA\Property(property="type", type="string", enum={"Normal", "VIP"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Salle créée"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer',
            'type' => 'required|string|in:Normal,VIP',
        ]);

        return response()->json($this->salleRepository->create($data), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/salles/{id}",
     *     summary="Afficher une salle spécifique",
     *     tags={"Salles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Salle trouvée"),
     *     @OA\Response(response=404, description="Salle non trouvée")
     * )
     */
    public function show($id)
    {
        $salle = $this->salleRepository->find($id);
        if (!$salle) {
            return response()->json(['message' => 'Salle non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($salle, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/salles/{id}",
     *     summary="Mettre à jour une salle",
     *     tags={"Salles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="capacite", type="integer"),
     *             @OA\Property(property="type", type="string", enum={"Normal", "VIP"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Salle mise à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Salle non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'capacite' => 'sometimes|integer',
            'type' => 'sometimes|string|in:Normal,VIP',
        ]);

        $salle = $this->salleRepository->update($id, $data);
        if (!$salle) {
            return response()->json(['message' => 'Salle non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($salle, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/salles/{id}",
     *     summary="Supprimer une salle",
     *     tags={"Salles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Salle supprimée"),
     *     @OA\Response(response=404, description="Salle non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->salleRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Salle non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Salle supprimée'], Response::HTTP_OK);
    }
}
