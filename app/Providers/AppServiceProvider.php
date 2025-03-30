<?php

namespace App\Providers;

use App\Repositories\Contracts\FilmRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\SalleRepositoryInterface;
use App\Repositories\Contracts\SeanceRepositoryInterface;
use App\Repositories\Contracts\SiegeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Repositories\SalleRepository;
use App\Repositories\FilmRepository;
use App\Repositories\SeanceRepository;
use App\Repositories\SiegeRepository;
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

        $this->app->bind(SalleRepositoryInterface::class, SalleRepository::class);

        $this->app->bind(SiegeRepositoryInterface::class, SiegeRepository::class);

        $this->app->bind(FilmRepositoryInterface::class, FilmRepository::class);

        $this->app->bind(SeanceRepositoryInterface::class, SeanceRepository::class);

        $this->app->bind(
            \App\Repositories\Contracts\ReservationRepositoryInterface::class,
            \App\Repositories\ReservationRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
