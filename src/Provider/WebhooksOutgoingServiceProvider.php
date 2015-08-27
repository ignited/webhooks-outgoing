<?php
namespace Ignited\Webhooks\Outgoing\Provider;

use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository;
use Ignited\Webhooks\Outgoing\Services\RequestService;
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
        $this->app->bind('webhooks', function($app)
        {
            return new Webhooks($app['webhooks.requests'], $app['webhooks.service']);
        });

        $this->app->singleton('webhooks.service', function ($app) {
            return new RequestService($app['webhooks.requests'], new Client(), $app['Illuminate\Contracts\Bus\Dispatcher'], $app['config']['webhooks-outgoing']);
        });

        $this->app->bind('Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface', function ($app) {
            $config = $this->app['config']['webhooks-outgoing'];

            $model = array_get($config, 'requests.model');

            return new IlluminateRequestRepository($model);
        });

        $this->app->alias('webhooks.requests', 'Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface');

        $this->app->alias('Ignited\Webhooks\Outgoing\Services\RequestServiceInterface', 'Ignited\Webhooks\Outgoing\Services\RequestService');
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/webhooks-outgoing.php'), 'webhooks-outgoing');
        $config = $this->app['config']['webhooks-outgoing'];
    }
}
