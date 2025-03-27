<?php
namespace App\Jobs;

use App\Models\Reservations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CancelExpiredReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function handle()
    {
        DB::beginTransaction();
        try {
            $expiredReservations = Reservations::where('status', 'en attente')
                ->where('expires_at', '<=', now())
                ->get();

            foreach ($expiredReservations as $reservation) {
                $paiements = DB::table('paiements')->where('reservation_id', $reservation->id)->get();

                foreach ($paiements as $paiement) {
                    DB::table('sieges')
                        ->where('id', $paiement->siege_id)
                        ->update(['status' => 'available']);
                }
                DB::table('paiements')->where('reservation_id', $reservation->id)->delete();

                $reservation->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur suppression des réservations expirées : " . $e->getMessage());
        }
    }


}
