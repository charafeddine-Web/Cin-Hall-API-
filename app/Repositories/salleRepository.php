<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Models\Salles;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SalleRepositoryInterface;

class salleRepository extends BaseRepository implements SalleRepositoryInterface
{
    public function __construct(Salles $salles)
    {
        parent::__construct($salles);
    }
}
