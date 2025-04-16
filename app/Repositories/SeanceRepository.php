<?php

namespace App\Repositories;

use App\Models\Film;
use App\Models\Salle;
use App\Models\Seance;
use App\Repositories\Contracts\SeanceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SeanceRepository implements SeanceRepositoryInterface
{
    public function getAll($filmId = null, $type = null)
    {
        $query = Seance::with('film', 'salle'); // charge les relations si besoin

        if ($filmId) {
            $query->where('film_id', $filmId);
        }

        if ($type) {
            $query->where('type_seance', $type);
        }

        return $query->get();    }

    public function findById($id)
{
    return Seance::find($id);
}
    public function getSeance($id)
    {
        return Seance::find($id);
    }
    public function getSeancesByType($type){
        $seances = DB::table('seances')
             ->join('films', 'films.id', '=', 'seances.film_id')
            ->where('seances.type_seance', $type)
            ->get();
        return $seances;

    }

//    public function create(array $data)
//    {
//        $film = Film::findOrFail($data['film_id']);
//        return $film->salles()->attach($data['salle_id'], [
//            'start_time' => $data['start_time'],
//            'session' => $data['session'],
//            'langue' => $data['langue']
//        ]);
//    }


    public function create(array $data)
    {
        $film = Film::findOrFail($data['film_id']);
        $salle = Salle::findOrFail($data['salle_id']);

        // Ajouter une séance dans la table pivot
        return $film->salles()->attach($salle->id, [
            'start_time' => $data['start_time'],
            'session' => $data['session'],
            'langue' => $data['langue'],
            'type_seance' => $data['type_seance'],
            'prix' => $data['prix'],
        ]);
    }
    public function update($id, array $data)
    {
        $seance = $this->findById($id);
        $seance->update($data);
        return $seance;
    }

    public function delete($id)
    {
        return Seance::destroy($id);
    }

    public function getAllSeancesWithFilms()
    {
       return DB::table('seances')
           ->join('films', 'films.id', '=', 'seances.film_id')
           ->join('salles', 'salles.id', '=', 'seances.salle_id')
           ->get();
    }
}
