<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use PDF;

class TicketController extends Controller
{
    // Génération de billets électroniques avec QR Code
    public function generateTicket($reservationId)
    {
        $ticket = Ticket::where('reservation_id', $reservationId)->first();
        $pdf = PDF::loadView('ticket.pdf', compact('ticket'));

        return $pdf->download('ticket-' . $ticket->id . '.pdf');
    }
}
