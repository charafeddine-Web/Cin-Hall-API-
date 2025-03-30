<?php
namespace App\Repositories;

use App\Models\Siege;
use App\Repositories\Contracts\SiegeRepositoryInterface;

class SiegeRepository implements SiegeRepositoryInterface
{
    public function getAll()
    {
        return Siege::all();
    }

    public function findById($id)
    {
        return Siege::findOrFail($id);
    }

    public function create(array $data)
    {
        return Siege::create($data);
    }

    public function update($id, array $data)
    {
        $siege = Siege::find($id);
        if ($siege) {
            $siege->update($data);
        }
        return $siege;
    }

    public function delete($id)
    {
        $siege = Siege::find($id);
        if ($siege) {
            return $siege->delete();
        }
        return false;
    }

    public function getSiege( $siege_id)
    {
        return Siege::find( $siege_id);
    }

}
