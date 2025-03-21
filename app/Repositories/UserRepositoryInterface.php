<?php
namespace App\Repositories;


interface UserRepositoryInterface extends BaseRepositoryInterface{
    public function finbByEmail($email);
}

