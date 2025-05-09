<?php

namespace App\Providers;

use App\Contracts\MessageRepositoryInterface;
use App\Contracts\MessageServiceInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomServiceInterface;
use App\Repositories\MessageRepository;
use App\Repositories\RoomRepository;
use App\Services\MessageService;
use App\Services\RoomService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);

        $this->app->bind(RoomServiceInterface::class, RoomService::class);
        $this->app->bind(MessageServiceInterface::class, MessageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
