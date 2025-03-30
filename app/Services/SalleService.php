<?php

namespace App\Services;

use App\Repositories\Contracts\SalleRepositoryInterface;

class SalleService
{
    protected $salleRepository;

    public function __construct(SalleRepositoryInterface $salleRepository)
    {
        $this->salleRepository = $salleRepository;
    }

    public function getAvailableSalles()
    {
        return $this->salleRepository->getAvailableSalles();
    }

    public function create(array $data)
    {
        return $this->salleRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->salleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->salleRepository->delete($id);
    }
    public function get($id) {
        return $this->salleRepository->find($id);
    }
    public function getAll() {
        return $this->salleRepository->getAll();
    }


}
