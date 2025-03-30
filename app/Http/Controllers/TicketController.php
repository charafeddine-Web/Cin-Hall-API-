<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Barryvdh\DomPDF\Facade\PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// Ajoutez l'import du QRCode
class TicketController extends Controller
{
    private $reservationService;
    public function __construct(ReservationService $reservationService){
        $this->reservationService = $reservationService;
    }
    /**
     * Génère le ticket PDF pour une réservation donnée.
     *
     * @param  int  $reservationId
     * @return \Illuminate\Http\Response
     */
    public function generateTicket($reservationId)
    {
        // Récupérer la réservation depuis la base de données
        $reservation = Reservation::findOrFail($reservationId);
        $detailleRes = $this->reservationService->ReservationDetaille($reservationId);
        // Préparer les données du ticket
        $data = [
            'customer_name' => 'cher spectateur /spectatrice', // Remplacez par le nom du client si nécessaire
            'Film' => $detailleRes['film']->titre ,
            'Seance' => $detailleRes['seance']->start_time,
            'Siege' => $detailleRes['siege']->siege_number,
            'Prix' => $detailleRes['seance']->prix,

            'reservation_code' => "test2554651", // Remplacez par un vrai code si nécessaire
        ];

        // Charger la vue avec les données
        $pdf = PDF::loadView('pdf.ticket', $data);

        // Retourner le PDF
        return $pdf->download('ticket-' . $reservation->reservation_code . '.pdf');
    }


////    avec qrcode

//    public function generateTicket($reservationId)
//    {
//        // Récupérer la réservation depuis la base de données
//        $reservation = Reservation::findOrFail($reservationId);
//        $detailleRes = $this->reservationService->ReservationDetaille($reservationId);
//
//        // Générer le QR code
//        $qrCode = QrCode::size(150)->generate($reservation->reservation_code); // Code QR avec le code de réservation
//
//        // Convertir le QR code en image base64
//        $qrCodeBase64 = base64_encode($qrCode);
//
//        // Préparer les données du ticket
//        $data = [
//            'customer_name' => 'Cher spectateur/spectatrice', // Remplacez par le nom du client si nécessaire
//            'Film' => $detailleRes['film']->titre,
//            'Seance' => $detailleRes['seance']->start_time,
//            'Siege' => $detailleRes['siege']->siege_number,
//            'Prix' => $detailleRes['seance']->prix,
//            'reservation_code' => $reservation->reservation_code, // Utilisez le vrai code de réservation
//            'qr_code' => $qrCodeBase64, // Ajoutez le QR code à la vue
//        ];
//
//        // Charger la vue avec les données
//        $pdf = PDF::loadView('pdf.ticket', $data);
//
//        // Retourner le PDF
//        return $pdf->download('ticket-' . $reservation->reservation_code . '.pdf');
//    }

}
