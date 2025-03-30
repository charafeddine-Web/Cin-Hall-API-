<?php

namespace App\Repositories\Contracts;

use App\Models\Seance;

interface ReservationRepositoryInterface
{
    public function checkAvailableSieges(Seance $seance);
    public function createReservation(array $data);
    public function confirmReservation($reservationId);
    public function cancelReservation($reservationId);


}
