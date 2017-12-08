<?php

namespace Ofertis\MediaServer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MediaServerServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/media-server.php' => config_path('media-server.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/media-server.php', 'media-server');

        $this->app->singleton('mediaserver', function (Application $app) {
            return new MediaServer($app->make('config')->get('media-server'));
        });
    }

    public function provides()
    {
        return ['mediaserver'];
    }
}
