<?php

namespace BlockUploads\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Filesystem\Events\FileStored;
use BlockUploads\Listeners\BlockInvalidUploads;
use BlockUploads\Middleware\RestrictFileUploads;

class BlockUploadsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/blockuploads.php' => config_path('blockuploads.php'),
        ], 'blockuploads-config');

        app('router')->pushMiddlewareToGroup('web', RestrictFileUploads::class);

        Event::listen(FileStored::class, [BlockInvalidUploads::class, 'handle']);
        Event::listen(RouteMatched::class, [BlockInvalidUploads::class, 'handlePostUploads']);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/blockuploads.php', 'blockuploads');
    }
}