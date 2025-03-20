<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class UserRepository extends  BaseRepository implements UserRepositoryInterface {

    public function __construct(User $user){
        parent::__construct($user);
    }

    public function finbByEmail($email){
        return $this->model->where('email',$email)->first();
    }
}
