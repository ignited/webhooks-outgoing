<?php
namespace Ignited\Webhooks\Outgoing\Provider;

use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository;
use Ignited\Webhooks\Outgoing\Webhooks;
use Illuminate\Support\ServiceProvider;
use \GuzzleHttp\Client;

class WebhooksOutgoingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setupConfig();
    }

    public function register()
    {
        $this->registerRequests();

        $this->app->bind('webhooks', function($app)
        {
            return new Webhooks($app['webhooks.requests'], new Client(), $app['Illuminate\Contracts\Bus\Dispatcher'], $app['config']['webhooks-outgoing']);
        });
    }

    public function registerRequests()
    {
        $this->app->singleton('webhooks.requests', function ($app) {
            $config = $this->app['config']['webhooks-outgoing'];

            $model = array_get($config, 'requests.model');

            return new IlluminateRequestRepository($model);
        });
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/webhooks-outgoing.php'), 'webhooks-outgoing');
        $config = $this->app['config']['webhooks-outgoing'];
    }
}
