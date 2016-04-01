<?php
namespace Ignited\Webhooks\Outgoing\Provider;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 9:27 AM
 */
class LaravelServiceProvider extends WebhooksOutgoingServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->publishes([
            realpath(__DIR__.'/../../config/webhooks-outgoing.php') => config_path('webhooks-outgoing.php'),
        ]);

        $migrations = realpath(__DIR__.'/../../migrations');

        $this->publishes([
            $migrations => $this->app->databasePath().'/migrations',
        ], 'migrations');
    }

    public function register()
    {
        parent::register();
    }
}