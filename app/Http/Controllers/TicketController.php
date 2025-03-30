<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Barryvdh\DomPDF\Facade\PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *     title="Ticket API",
 *     version="1.0.0",
 *     description="API for generating PDF tickets for reservations",
 * )
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Localhost API Server"
 * )
 */
class TicketController extends Controller
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * @OA\Get(
     *     path="/tickets/{reservationId}",
     *     summary="Generate a PDF ticket for a given reservation",
     *     description="Generates a ticket in PDF format with reservation details and QR code.",
     *     operationId="generateTicket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         required=true,
     *         description="ID of the reservation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket PDF generated successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     ),
     * )
     */
    public function generateTicket($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $detailleRes = $this->reservationService->ReservationDetaille($reservationId);

        $data = [
            'customer_name' => $detailleRes['user']->name,
            'Film' => $detailleRes['film']->titre,
            'Seance' => $detailleRes['seance']->start_time,
            'Siege' => $detailleRes['siege']->siege_number,
            'Prix' => $detailleRes['seance']->prix,
            'reservation_code' => "test2554651",
        ];

        $pdf = PDF::loadView('pdf.ticket', $data);
        return $pdf->download('ticket-' . $reservation->reservation_code . '.pdf');
    }

// Example method with QR code
// /**
//  * @OA\Get(
//  *     path="/tickets/{reservationId}/qrcode",
//  *     summary="Generate a PDF ticket with QR code",
//  *     description="Generates a ticket in PDF format with a reservation QR code.",
//  *     operationId="generateTicketWithQRCode",
//  *     tags={"Tickets"},
//  *     @OA\Parameter(
//  *         name="reservationId",
//  *         in="path",
//  *         required=true,
//  *         description="ID of the reservation",
//  *         @OA\Schema(type="integer")
//  *     ),
//  *     @OA\Response(
//  *         response=200,
//  *         description="Ticket PDF with QR code generated successfully",
//  *         @OA\MediaType(
//  *             mediaType="application/pdf"
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=404,
//  *         description="Reservation not found"
//  *     ),
//  * )
//  */
// public function generateTicketWithQRCode($reservationId)
// {
//     $reservation = Reservation::findOrFail($reservationId);
//     $detailleRes = $this->reservationService->ReservationDetaille($reservationId);
//     $qrCode = QrCode::size(150)->generate($reservation->reservation_code);
//     $qrCodeBase64 = base64_encode($qrCode);
//     $data = [
//         'customer_name' => 'Cher spectateur/spectatrice',
//         'Film' => $detailleRes['film']->titre,
//         'Seance' => $detailleRes['seance']->start_time,
//         'Siege' => $detailleRes['siege']->siege_number,
//         'Prix' => $detailleRes['seance']->prix,
//         'reservation_code' => $reservation->reservation_code,
//         'qr_code' => $qrCodeBase64,
//     ];
//     $pdf = PDF::loadView('pdf.ticket', $data);
//     return $pdf->download('ticket-' . $reservation->reservation_code . '.pdf');
// }
}
