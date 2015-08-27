<?php
namespace Ignited\Webhooks\Outgoing;

use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Laravel\Lumen\Routing\DispatchesJobs;

/**
 * Class Webhooks
 * @package Ignited\Webhooks\Outgoing
 */
class Webhooks
{
    use DispatchesJobs;

    protected $requests;
    protected $client;

    public function __construct(RequestRepositoryInterface $requests,
                                ClientInterface $client)
    {
        $this->requests = $requests;
        $this->client = $client;
    }

    public function generate($url, $body, $method='post')
    {
        $request = $this->create(compact(['url', 'body', 'method']));

        return $request;
    }

    public function create($data)
    {
        $request = $this->requests->create($data);

        return $request;
    }

    public function dispatch(RequestInterface $request)
    {
        if($this->requests->save($request))
        {
            $job = (new WebhookJob($request));

            app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
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