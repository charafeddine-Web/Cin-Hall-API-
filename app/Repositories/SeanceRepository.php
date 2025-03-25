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
    public function getByType(string $type): Collection
    {
        return Seances::where('type', $type)->get();
    }
}
