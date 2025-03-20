<?php

namespace App\Repositories;


interface BaseRepositoryInterface {
    public function all();
    public function find($id);

    public function create(array $attributes);

    public function upadte($id, array $attributes);

    public function delete($id);

}
