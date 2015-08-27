<?php
namespace Ignited\Webhooks\Outgoing\Provider;

use Ignited\Webhooks\Outgoing\Webhooks;
use Illuminate\Support\ServiceProvider;

class WebhooksOutgoingServiceProvider extends ServiceProvider
{
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    public function boot()
    {
        $this->setupConfig();
    }

    public function register()
    {
        $this->app->bind('webhooks', function($app)
        {
            return new Webhooks();
        });
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/webhooks-outgoing.php'), 'webhooks-outgoing');
        $config = $this->app['config']['webhooks-outgoing'];
    }
}
