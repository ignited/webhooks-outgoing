<?php
namespace Ignited\Webhooks\Outgoing;

use Illuminate\Support\ServiceProvider;

class WebhooksOutgoingServiceProvider extends ServiceProvider
{
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    public function register()
    {
        $this->app->bind('webhooks', function($app)
        {
            return new Webhooks;
        });
    }

    public function boot()
    {

    }
}
