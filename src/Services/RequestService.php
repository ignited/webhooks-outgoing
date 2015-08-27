<?php
namespace Ignited\Webhooks\Outgoing\Services;
use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Mockery as m;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 1:46 PM
 */
class RequestService implements RequestServiceInterface
{
    public function __construct(RequestRepositoryInterface $requests,
                                ClientInterface $client,
                                Dispatcher $dispatcher,
                                $config
    )
    {
        $this->requests = $requests;
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->config = $config;
    }

    public function dispatch(RequestInterface $request)
    {
        if($this->requests->save($request))
        {
            $job = (new WebhookJob($request, $this->config, m::mock('Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface'), m::mock('Ignited\Webhooks\Outgoing\Services\RequestService')));

            $this->dispatcher->dispatch($job);
        }
    }

    public function fire(RequestInterface $request)
    {
        $request = new \GuzzleHttp\Psr7\Request($request->getMethod(), $request->getUrl(), [], json_encode($request->getBody()));

        $response = $this->send($request);

        return $response;
    }

    public function send(\Psr\Http\Message\RequestInterface $request)
    {
        return $this->client->send($request);
    }
}