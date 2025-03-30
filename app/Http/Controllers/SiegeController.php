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

    public function index()
    {
        return response()->json($this->siegeService->getAllSieges());
    }

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

    public function show($id)
    {
        $siege = $this->siegeService->getSiegeById($id);
        if (!$siege) {
            return response()->json(['message' => 'Siège non trouvé'], 404);
        }
        return response()->json($siege);
    }

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

    public function destroy($id)
    {
        $this->siegeService->deleteSiege($id);
        return response()->json(['message' => 'Siège supprimé avec succès']);
    }
}
