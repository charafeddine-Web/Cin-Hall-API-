<?php

namespace App\Providers;

use App\Repositories\FilmRepository;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\SalleRepositoryInterface;
use App\Repositories\Interfaces\SeanceRepositoryInterface;
use App\Repositories\Interfaces\SeatRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Repositories\salleRepository;
use App\Repositories\SeanceRepository;
use App\Repositories\SeatRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(FilmRepositoryInterface::class, FilmRepository::class);
        $this->app->bind(SalleRepositoryInterface::class, SalleRepository::class);
        $this->app->bind(SeanceRepositoryInterface::class, SeanceRepository::class);
        $this->app->bind(SeatRepositoryInterface::class, SeatRepository::class);
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
