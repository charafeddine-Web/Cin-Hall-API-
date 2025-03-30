<?php
namespace App\Repositories;
use App\Models\Salle;
use App\Repositories\Contracts\SalleRepositoryInterface;

class SalleRepository implements SalleRepositoryInterface
{
    public function getAll()
    {
        return Salle::all();
    }
    public function getAvailableSalles()
    {
        // TODO: Implement getAvailableSalles() method.
    }

    public function find($id)
    {
        return salle::find($id) ;
    }

    public function create(array $data){
        return Salle::create($data);
    }
    public function update($id, array $data)
    {
        $salle = Salle::find($id);

        if (!$salle) {
            return response()->json(['message' => 'Salle non trouvée.'], 404);
        }

        $salle->update($data);

        return response()->json([
            'message' => 'Salle mise à jour avec succès.',
            'salle' => $salle
        ], 200);
    }

    public function delete($id)
    {
        $salle = Salle::find($id);
//        return response()->json(["hh" => "ddd."], 404);
        if (!$salle) {
            return response()->json(["error" => "Aucune salle trouvée avec cet identifiant."], 404);
        }

        if ($salle->delete()) {
            return response()->json(["message" => "Salle supprimée avec succès."], 200);
        }

        return response()->json(["error" => "Erreur lors de la suppression de la salle."], 500);
    }

}
