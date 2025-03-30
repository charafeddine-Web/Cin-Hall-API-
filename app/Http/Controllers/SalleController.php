<?php
namespace App\Http\Controllers;

use App\Services\SalleService;
use App\Services\SiegeService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      title="CinéHall API Documentation",
 *      version="1.0.0",
 *      description="Documentation de l'API CinéHall"
 * )
 *
 * @OA\Tag(
 *     name="Salles",
 *     description="Gestion des salles de cinéma"
 * )
 */
class SalleController extends Controller
{
    protected $salleService;
    protected $siegeService;

    public function __construct(SalleService $salleService, SiegeService $siegeService)
    {
        $this->salleService = $salleService;
        $this->siegeService = $siegeService;
    }

    /**
     * @OA\Get(
     *     path="/api/salles",
     *     summary="Liste toutes les salles",
     *     description="Récupère la liste de toutes les salles de cinéma.",
     *     tags={"Salles"},
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nom", type="string", example="Salle 1"),
     *                 @OA\Property(property="capacite", type="integer", example=100),
     *                 @OA\Property(property="type", type="string", example="Normale")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json($this->salleService->getAll(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/salles",
     *     summary="Créer une nouvelle salle",
     *     description="Ajoute une nouvelle salle et génère automatiquement les sièges.",
     *     tags={"Salles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "capacite", "type"},
     *             @OA\Property(property="nom", type="string", example="Salle 1"),
     *             @OA\Property(property="capacite", type="integer", example=100),
     *             @OA\Property(property="type", type="string", enum={"Normale", "VIP"}, example="VIP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Salle créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="salle", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nom", type="string", example="Salle 1"),
     *                 @OA\Property(property="capacite", type="integer", example=100),
     *                 @OA\Property(property="type", type="string", example="VIP")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer',
            'type' => 'required|in:Normale,VIP',
        ]);

        $salle = $this->salleService->create($validated);
        $sieges = $this->siegeService->generateSiegesForSalle($salle);

        return response()->json(['salle' => $salle, 'sieges' => $sieges], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/salles/{id}",
     *     summary="Modifier une salle existante",
     *     tags={"Salles"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la salle", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Salle mise à jour avec succès")
     * )
     */
    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer',
            'type' => 'required|in:Normale,VIP',
        ]);

        $salle = $this->salleService->update($id, $validated);
        return response()->json(['salle' => $salle], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/salles/{id}",
     *     summary="Supprimer une salle",
     *     tags={"Salles"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la salle", @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Salle supprimée avec succès")
     * )
     */
    public function destroy($id)
    {
        $this->salleService->delete($id);
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/salles/{id}",
     *     summary="Récupérer une salle",
     *     tags={"Salles"},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la salle", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails de la salle", @OA\JsonContent(type="object"))
     * )
     */
    public function show($id)
    {
        return response()->json($this->salleService->get($id), 200);
    }
}
