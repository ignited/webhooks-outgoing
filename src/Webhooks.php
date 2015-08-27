<?php
namespace Ignited\Webhooks\Outgoing;

use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;

/**
 * Class Webhooks
 * @package Ignited\Webhooks\Outgoing
 */
class Webhooks
{
    protected $requests;
    protected $client;
    protected $dispatcher;
    protected $config;

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

    public function generate($url, $body, $method='post')
    {
        $request = $this->requests->create(compact(['url', 'body', 'method']));

        return $request;
    }

    public function create($data)
    {
        $request = $this->requests->create($data);

        return $request;
    }

    public function delete(RequestInterface $request)
    {
        return $this->requests->delete($request);
    }

    public function update(RequestInterface $request)
    {
        return $this->requests->save($request);
    }

    public function dispatch(RequestInterface $request)
    {
        if($this->requests->save($request))
        {
            $job = (new WebhookJob($request, $this->config));

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