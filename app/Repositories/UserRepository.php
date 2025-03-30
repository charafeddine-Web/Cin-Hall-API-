<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface

{
  public function create(array $user){
      // dans query builder apres l inseration on return true ou false a l invers de eloquant
      // la methode   $user = User::create($userData);  qui recupere l user des le debut
      $userId = DB::table('users')->insertGetId($user);
      // Récupérer l'utilisateur en tant qu'objet User
      return $this->findById($userId);
  }
  public function update( array $user , $id,){
      DB::table('users')->where('id' , '=' , $id)->update($user);
      return response()->json(['message'=>'user modifie avec succes'], 201) ;
  }
  public function delete($id){
      DB::table('users')->where('id' , '=' , $id)->delete();
      return response()->json(['message'=>'user supprime avec succes'], 201);
  }
  public function findById($id){
      return User::find($id);

  }
  public function findByEmail($email){
     return   $user = DB::table('users')->where('email' , '=' , $email)->first();
  }
  public function findByRole($role){
      $user = DB::table('users')->where('role' , '=' , $role)->first();

  }


    public function getUser($user_id)
    {
        return User::find($user_id);
    }
}
