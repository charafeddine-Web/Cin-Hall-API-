<?php


namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CancelPendingReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Exécute le Job.
     */
    public function handle()
    {
        $limit = Carbon::now()->subMinutes(15);
        $reservations = Reservation::where('status', 'pending')
            ->where('created_at', '<', $limit)
            ->update(['status' => 'cancelled']);
        if ($reservations > 0) {
            \Log::info("{$reservations} réservations en attente annulées après 15 minutes.");
        }
    }
}
