<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\ImageSearchInterface;
use App\Services\Image\UnsplashImageSearch;

class ImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ImageSearchInterface::class, UnsplashImageSearch::class);
    }
}