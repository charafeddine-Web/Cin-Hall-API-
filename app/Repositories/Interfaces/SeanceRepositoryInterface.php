<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface SeanceRepositoryInterface extends BaseRepositoryInterface
{
    public function getByType(string $type): Collection;
}
