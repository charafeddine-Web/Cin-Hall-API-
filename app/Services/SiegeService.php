<?php

namespace App\Services;


use App\Repositories\SiegeRepository;

class SiegeService
{
    protected $siegeRepo;

    public function __construct(SiegeRepository $siegeRepo)
    {
        $this->siegeRepo = $siegeRepo;
    }

    public function getAllSieges()
    {
        return $this->siegeRepo->getAll();
    }

    public function getSiegeById($id)
    {
        return $this->siegeRepo->find($id);
    }

    public function createSiege(array $data)
    {
        return $this->siegeRepo->create($data);
    }

    public function updateSiege($id, array $data)
    {
        return $this->siegeRepo->update($id, $data);
    }

    public function deleteSiege($id)
    {
        return $this->siegeRepo->delete($id);
    }

    public function generateSiegesForSalle($salle)
    {
        $sieges = [];

        for ($i = 1; $i <= $salle->capacite; $i++) {
            $type = ($salle->type_seance === 'VIP' && $i % 2 == 1) ? 'couple' : 'standard';
            $sieges[] = $this->siegeRepo->create([
                'salle_id' => $salle->id,
                'numero' => 'S' . $i,
                'type' => $type,
            ]);
        }

        return $sieges;
    }
}
