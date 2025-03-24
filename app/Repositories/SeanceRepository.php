<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Models\Salles;
use App\Models\Seances;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SeanceRepositoryInterface;

class SeanceRepository extends BaseRepository implements SeanceRepositoryInterface
{
    public function __construct(Seances $seances)
    {
        parent::__construct($seances);
    }
}
