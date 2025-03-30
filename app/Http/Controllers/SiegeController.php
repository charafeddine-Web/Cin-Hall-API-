<?php
namespace App\Http\Controllers;

use App\Services\SiegeService;
use Illuminate\Http\Request;


class SiegeController extends Controller
{
    protected $siegeService;

    public function __construct(SiegeService $siegeService)
    {
        $this->siegeService = $siegeService;
    }

    /**
     * @OA\Get(
     *     path="/api/sieges",
     *     summary="Liste tous les sièges",
     *     tags={"Sièges"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des sièges",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="salle_id", type="integer", example=2),
     *                 @OA\Property(property="numero", type="string", example="A1"),
     *                 @OA\Property(property="type", type="string", example="standard")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json($this->siegeService->getAllSieges());
    }

    /**
     * @OA\Post(
     *     path="/api/sieges",
     *     summary="Créer un nouveau siège",
     *     tags={"Sièges"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"salle_id", "numero", "type"},
     *             @OA\Property(property="salle_id", type="integer", example=2),
     *             @OA\Property(property="numero", type="string", example="A1"),
     *             @OA\Property(property="type", type="string", enum={"standard", "couple"}, example="standard")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Siège créé avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'numero' => 'required|string|max:10',
            'type' => 'required|in:standard,couple',
        ]);

        $siege = $this->siegeService->createSiege($validated);
        return response()->json($siege, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/sieges/{id}",
     *     summary="Afficher un siège spécifique",
     *     tags={"Sièges"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du siège",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du siège",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="salle_id", type="integer", example=2),
     *             @OA\Property(property="numero", type="string", example="A1"),
     *             @OA\Property(property="type", type="string", example="standard")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $siege = $this->siegeService->getSiegeById($id);
        if (!$siege) {
            return response()->json(['message' => 'Siège non trouvé'], 404);
        }
        return response()->json($siege);
    }

    /**
     * @OA\Put(
     *     path="/api/sieges/{id}",
     *     summary="Modifier un siège",
     *     tags={"Sièges"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du siège",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="numero", type="string", example="B1"),
     *             @OA\Property(property="type", type="string", enum={"standard", "couple"}, example="couple"),
     *             @OA\Property(property="reserve", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Siège mis à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'numero' => 'string|max:10',
            'type' => 'in:standard,couple',
            'reserve' => 'boolean',
        ]);

        $siege = $this->siegeService->updateSiege($id, $validated);
        if (!$siege) {
            return response()->json(['message' => 'Siège non trouvé'], 404);
        }
        return response()->json($siege);
    }

    /**
     * @OA\Delete(
     *     path="/api/sieges/{id}",
     *     summary="Supprimer un siège",
     *     tags={"Sièges"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du siège",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Siège supprimé avec succès"
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->siegeService->deleteSiege($id);
        return response()->json(['message' => 'Siège supprimé avec succès']);
    }
}
