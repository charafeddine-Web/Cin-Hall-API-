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
        return $this->model->create($data);
    }
    public function update($id, array $data){
        return $this->model->update($id, $data);
    }
    public function delete($id){
        return $this->model->destroy($id);
    }
    public function find($id){
        return $this->model->find($id);
    }

}
