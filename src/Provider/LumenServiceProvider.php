<?php
namespace Ignited\Webhooks\Outgoing\Provider;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 9:27 AM
 */
class LumenServiceProvider extends WebhooksOutgoingServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    protected function setupConfig()
    {
        $this->app->configure('webhooks-outgoing');
        parent::setupConfig();
    }

    public function register()
    {
        parent::register();
    }
}