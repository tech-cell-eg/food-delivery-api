<?php

namespace App\Providers;

use App\Repository\UserRepository;
use App\RepositoryInterface\UserInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsCheif;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //

    $this->app->bind(UserInterface::class, UserRepository::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //

  }
}
