<?php

namespace App\Repositories;

use App\Models\Filmes;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\FilmRepositoryInterface;

class FilmRepository extends BaseRepository implements FilmRepositoryInterface
{
    public function __construct(Filmes $film)
    {
        parent::__construct($film);
    }
}
