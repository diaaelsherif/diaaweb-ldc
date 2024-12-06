<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CourseRepositoryInterface;
use App\Repository\CourseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class,CourseRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
