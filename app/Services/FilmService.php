<?php


namespace App\Services;

use App\Repositories\Contracts\FilmRepositoryInterface;

class FilmService
{
    protected $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function getAvailableFilms()
    {
        return $this->filmRepository->getAvailableFilms();
    }

    public function create(array $data)
    {
        return $this->filmRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->filmRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->filmRepository->delete($id);
    }

    public function get($id)
    {
        return $this->filmRepository->find($id);
    }

    public function getAll()
    {
        return $this->filmRepository->getAll();
    }
}
