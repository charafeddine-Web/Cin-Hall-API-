<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Models\Salles;
use App\Models\Seances;
use App\Models\Sieges;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SeanceRepositoryInterface;
use App\Repositories\Interfaces\SeatRepositoryInterface;

class SeatRepository extends BaseRepository implements SeatRepositoryInterface
{
    public function __construct(Sieges $sieges)
    {
        parent::__construct($sieges);
    }
}
