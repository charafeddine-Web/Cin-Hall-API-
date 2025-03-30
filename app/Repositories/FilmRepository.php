<?php


namespace App\Repositories;

use App\Models\Film;
use App\Repositories\Contracts\FilmRepositoryInterface;

class FilmRepository implements FilmRepositoryInterface
{

    public function create(array $data)
    {
        return Film::create($data);
    }

    public function update($id, array $data)
    {
        $film = Film::findOrFail($id);
        $film->update($data);
        return $film;
    }

    public function delete($id)
    {
        $film = Film::findOrFail($id);
        $film->delete();
        return $film;
    }

    public function find($id)
    {
        return Film::findOrFail($id);
    }

    public function getAll()
    {
        return Film::all();
    }

    public function getFilm($film_id)
    {
       return   Film::findOrFail($film_id);
    }
}

