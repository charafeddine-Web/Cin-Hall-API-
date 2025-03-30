<?php

namespace App\Services;

use App\Repositories\Contracts\FilmRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\SeanceRepositoryInterface;
use App\Repositories\Contracts\SiegeRepositoryInterface;
use App\Repositories\UserRepository;
use function PHPUnit\Framework\isEmpty;

class ReservationService
{
    protected $reservationRepository;
    private $seanceRepository;
    private $siegeRepository;
    private $filmRepository;
    private $UserRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository , SeanceRepositoryInterface $seanceRepository , SiegeRepositoryInterface $siegeRepository , SiegeRepositoryInterface $siegeService , filmRepositoryInterface $filmRepository , userRepository $userRepository)
    {
        $this->reservationRepository = $reservationRepository;
        $this->seanceRepository = $seanceRepository;
        $this->siegeRepository = $siegeRepository;
        $this->filmRepository = $filmRepository;
        $this->userRepository = $userRepository;
    }

    public function createReservation(array $data)
    {
        // Récupérer la séance et les sièges (collection)
        $seance = $this->seanceRepository->getSeance($data['seance_id']);
        $sieges = $this->siegeRepository->getSiege($data['siege_id']); // Pas de first(), on veut une collection ici.

        if ($sieges->isEmpty()) {
            return response()->json(['error' => 'Il y a un ou plusieurs sièges inexistants.'], 404);
        }

        if (empty($seance)) {
            return response()->json(['error' => 'Il n\'y a pas de séance avec cet ID'], 403);
        }

        // Vérification de la salle des sièges
        foreach ($sieges as $siege) {
            if ($seance->salle_id != $siege->salle_id) {
                return response()->json(['error' => 'Ce siège n\'existe pas dans la salle où se déroule la séance'], 403);
            }
        }

        $availableSieges = $this->reservationRepository->checkAvailableSieges($seance);

        if ($availableSieges->isEmpty()) {
            return response()->json(['message' => 'Aucun siège disponible pour cette séance.'], 400);
        }

        // Logique pour la réservation VIP ou normale
        if ($seance->isVIP()) {
            if ($availableSieges->count() < 2) {
                return response()->json(['message' => 'Il n\'y a pas de sièges doubles disponibles pour cette séance VIP.'], 400);
            }

            // Réserver deux sièges
            $siege1 = $availableSieges->first();
            $siege2 = $availableSieges->skip(1)->first();

            $this->reservationRepository->createReservation([
                'user_id' => $data['user_id'],
                'siege_id' => $siege1->id,
                'seance_id' => $seance->id,
                'status' => 'pending',
                'prix' => $seance->prix,
            ]);

            $this->reservationRepository->createReservation([
                'user_id' => $data['user_id'],
                'siege_id' => $siege2->id,
                'seance_id' => $seance->id,
                'status' => 'pending',
                'prix' => $seance->prix,
            ]);

            return response()->json(['message' => 'Réservation ajoutée avec succès, paiement dans les 15 minutes.'], 200);
        }

        // Vérification de la disponibilité des sièges pour une séance normale
        foreach ($sieges as $siege) {
            if (!$availableSieges->contains('id', $siege->id)) {
                return response()->json([
                    'message' => 'Aucun siège disponible pour cette séance.',
                    'sièges_disponibles' => $availableSieges->pluck('id') // Renvoie uniquement les IDs des sièges disponibles
                ], 400);
            }

            // Création de la réservation pour chaque siège
            $this->reservationRepository->createReservation([
                'user_id' => $data['user_id'],
                'siege_id' => $siege->id,
                'seance_id' => $seance->id,
                'status' => 'pending',
                'prix' => $seance->prix,
            ]);
        }

        return response()->json(['message' => 'Réservation ajoutée avec succès, paiement dans les 15 minutes.'], 200);
    }


    public function updateResevation($data, $reservationId)
    {
        // Récupérer la réservation
        $reservation = $this->reservationRepository->getReservation($reservationId);

        // Récupérer la séance et le siège
        $seance = $this->seanceRepository->getSeance($data['seance_id']);
        $siege = $this->siegeRepository->getSiege($data['siege_id']);

        // Vérifier que l'utilisateur est bien le propriétaire de la réservation
        if ($reservation->user_id != $data['user_id']) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette réservation.'], 403);
        }

        // Vérification de l'existence de la séance et du siège
        if (is_null($seance)) {
            return response()->json(['error' => 'Il n\'y a pas de séance avec cet ID'], 403);
        }

        if (is_null($siege)) {
            return response()->json(['error' => 'Il n\'y a pas de siège avec cet ID'], 403);
        }

        // Vérifier que le siège appartient à la salle de la séance
        if ($seance->salle_id != $siege->salle_id) {
            return response()->json(['error' => 'Ce siège n\'existe pas dans la salle où se déroule la séance'], 403);
        }

        // Vérification de la disponibilité des sièges via le repository
        $availableSieges = $this->reservationRepository->checkAvailableSieges($seance);

        // Logique pour la réservation VIP
        if ($seance->isVIP()) {
            if ($availableSieges->count() < 2) {
                return response()->json(['message' => 'Il n\'y a pas de sièges doubles disponibles pour cette séance VIP.'], 400);
            }

            // Réserver deux sièges et mettre à jour les réservations
            $siege1 = $availableSieges->first();
            $siege2 = $availableSieges->skip(1)->first();

            // Mise à jour de la première réservation
            $this->reservationRepository->updateReservation($reservation, [
                'user_id' => $data['user_id'],
                'siege_id' => $siege1->id,
                'seance_id' => $seance->id,
                'status' => $reservation->status,
            ]);

            // Créer une seconde réservation pour le deuxième siège
            $this->reservationRepository->updateReservation($reservation, [
                'user_id' => $data['user_id'],
                'siege_id' => $siege2->id,
                'seance_id' => $seance->id,
                'status' => $reservation->status,
            ]);

            return response()->json(['message' => 'Réservation modifiée avec succès, paiement dans les 15 minutes.'], 200);
        }

        // Si la séance n'est pas VIP, vérifier la disponibilité du siège
        if (!$availableSieges->contains('id', $siege->id)) {
            return response()->json([
                'message' => 'Aucun siège disponible pour cette séance.',
                'contains' => $availableSieges->contains('id', $siege->id) // Afficher si le siège est disponible
            ], 400);
        }

        // Mettre à jour la réservation pour le siège simple
        $this->reservationRepository->updateReservation($reservation, [
            'user_id' => $data['user_id'],
            'siege_id' => $siege->id,
            'seance_id' => $seance->id,
            'status' => $reservation->status,
        ]);

        return response()->json(['message' => 'Réservation modifiée avec succès, paiement dans les 15 minutes.'], 200);
    }


    public function confirmReservation($reservationId)
    {
        return $this->reservationRepository->confirmReservation($reservationId);
    }

    public function cancelReservation($reservationId)
    {
        return $this->reservationRepository->cancelReservation($reservationId);
    }



    public function updateReservationStatus($reservationId)
    {
           $reservation = $this->reservationRepository->getReservation($reservationId);
            $this->reservationRepository->updateReservation($reservation, ['status' => 'reserved']);
        return response()->json(['message' => 'Statut mis à jour avec succès.', 'reservation' => $reservation], 200);
    }


    public function ReservationDetaille($reservationId)
    {
    $reservation = $this->reservationRepository->getReservation($reservationId);
        // Récupérer la séance et le siège
        $seance = $this->seanceRepository->getSeance($reservation->seance_id);
        $siege = $this->siegeRepository->getSiege($reservation->siege_id);
        $film = $this->filmRepository->getFilm($seance->film_id);
       // $user = $this->UserRepository->getUser($reservation->user_id);
        $data = [
            'reservation' => $reservation,
            'seance' => $seance,
            'siege' => $siege,
            'film' => $film,
          //  'user' => $user,
        ];
        return $data ;
    }

}
