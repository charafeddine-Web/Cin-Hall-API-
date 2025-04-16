<?php

namespace App\Services;

use App\Repositories\Contracts\SeanceRepositoryInterface;

class SeanceService
{
    protected $seanceRepository;

    public function __construct(SeanceRepositoryInterface $seanceRepository)
    {
        $this->seanceRepository = $seanceRepository;
    }

    public function getAllSeances($filmId = null, $type = null)
    {
        return $this->seanceRepository->getAll($filmId = null, $type = null);
    }

    public function createSeance(array $data)
    {
        return $this->seanceRepository->create($data);
    }

    public function updateSeance($id, array $data){
        return $this->seanceRepository->update($id, $data);
    }
    public function deleteSeance($id){
        return $this->seanceRepository->delete($id);
    }


    public function getSeancesByType($type)
    {
        if($type == "Normale" || $type == "VIP") {
            $seances = $this->seanceRepository->getSeancesByType($type);
            if ($seances->count() > 0) {
                return response()->json($seances, 200);
            } else {
                return response()->json(['message' => 'Il n\'y a pas de séance sous ce type'], 404);
            }
        } else {
            return response()->json(['error' => 'Vous devez choisir entre Vip et Normale'], 400);
        }
    }
    public function getAllSeancesWithFilms(){
        $seances=$this->seanceRepository->getAllSeancesWithFilms();
        if(!$seances->isEmpty()){
            return response()->json($seances , 200) ;
        } else {
            return response()->json(['error'=>'pas de seances a affiches'] , 404);
        }

    }

}
