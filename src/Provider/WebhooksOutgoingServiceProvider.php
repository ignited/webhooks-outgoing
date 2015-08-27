<?php
namespace Ignited\Webhooks\Outgoing\Provider;

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
        $this->app->bind('Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface', function ($app) {
            $config = $this->app['config']['webhooks-outgoing'];

            $model = array_get($config, 'requests.model');

            return new IlluminateRequestRepository($model);
        });

        $this->app->bind('Ignited\Webhooks\Outgoing\Services\RequestServiceInterface', function ($app) {
            return new RequestService($app['Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface'], new Client(), $app['Illuminate\Contracts\Bus\Dispatcher'], $app['config']['webhooks-outgoing']);
        });

        $this->app->bind('webhooks', function($app)
        {
            return new Webhooks($app['Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface'], $app['Ignited\Webhooks\Outgoing\Services\RequestServiceInterface']);
        });
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/webhooks-outgoing.php'), 'webhooks-outgoing');
        $config = $this->app['config']['webhooks-outgoing'];
    }
}
