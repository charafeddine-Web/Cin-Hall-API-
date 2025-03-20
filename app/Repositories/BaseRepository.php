<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface{

    protected $model;
    public function __construct(Model $model){
        return $this->model = $model;
    }

    public function all(){
        return $this->model->all();
    }
    public function create(array $data){

    }
    public function update($id, array $data){

    }
    public function delete($id){

    }
    public function find($id){

    }



}
