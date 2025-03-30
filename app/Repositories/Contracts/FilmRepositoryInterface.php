<?php

namespace App\Repositories\Contracts;

interface FilmRepositoryInterface
{
    public function getAll();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function find($id);

    public function getFilm($film_id);
}

