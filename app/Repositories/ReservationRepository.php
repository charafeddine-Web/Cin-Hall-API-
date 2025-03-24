<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Models\Reservations;
use App\Models\Salles;
use App\Models\Seances;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\SeanceRepositoryInterface;

class ReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    public function __construct(Reservations $reservations)
    {
        parent::__construct($reservations);
    }
}
