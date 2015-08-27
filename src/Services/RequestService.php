<?php
namespace Ignited\Webhooks\Outgoing\Services;
use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Mockery as m;

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
        $request->attempts += 1;

        $request->last_attempt_at = $request->freshTimestamp();

        try {
            $outRequest = new \GuzzleHttp\Psr7\Request($request->getMethod(), $request->getUrl(), [], json_encode($request->getBody()));

            $response = $this->send($outRequest);

            $request->response_code = $response->getStatusCode();

            $this->requests->save($request);

            return $response;
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                $request->response_code = $response->getStatusCode();
            }

            $this->requests->save($request);

            throw $e;
        }
    }

    public function getDelayInSeconds($request)
    {
        return (2 ^ $request->attempts);
    }

    public function send(\Psr\Http\Message\RequestInterface $request)
    {
        return $this->client->send($request);
    }
}