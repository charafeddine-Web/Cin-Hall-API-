<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Models\Salles;
use App\Models\Seances;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SeanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SeanceRepository extends BaseRepository implements SeanceRepositoryInterface
{
    public function __construct(Seances $seances)
    {
        parent::__construct($seances);
    }



    /**
     * Get all sessions filtered by type (VIP, Normal, etc.)
     *
     * @param string|null $type
     * @return Collection
     */
    public function getAllFiltered($type = null): Collection
    {
        $query = Seances::query();
        if ($type) {
            $query->where('session', $type);
        }
        if (request()->has('langue')) {
            $query->where('langue', request()->get('langue'));
        }
        if (request()->has('film_id')) {
            $query->where('film_id', request()->get('film_id'));
        }
        if (request()->has('salle_id')) {
            $query->where('salle_id', request()->get('salle_id'));
        }
        return $query->get();
    }
}
