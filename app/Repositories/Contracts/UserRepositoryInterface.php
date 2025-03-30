<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public  function create(array $data);
    public function update(array $data, $id);
    public function delete($id);

    public  function findByEmail($email);
    public  function findById($id);
    public  function findByRole($role);
    public  function getUser($user_id);


}
