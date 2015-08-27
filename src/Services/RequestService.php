<?php
namespace Ignited\Webhooks\Outgoing\Services;
use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Ignited\Webhooks\Outgoing\Traits\EventTrait;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Events\Dispatcher as EventDispatcher;

class RequestService implements RequestServiceInterface
{

    public function __construct(RequestRepositoryInterface $requests,
                                ClientInterface $client,
                                Dispatcher $dispatcher,
                                EventDispatcher $eventDispatcher,
                                $config
    )
    {
        $this->requests = $requests;
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
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
        $request->attempts += 1;

        $request->last_attempt_at = $request->freshTimestamp();

        try {
            $httpRequest = new \GuzzleHttp\Psr7\Request($request->getMethod(), $request->getUrl(), [], json_encode($request->getBody()));

            $this->eventDispatcher->fire('webhooks.sending', ['request'=>$request, 'httpRequest'=>$httpRequest]);

            $response = $this->send($httpRequest);

            $request->response_code = $response->getStatusCode();

            $this->requests->save($request);

            $this->eventDispatcher->fire('webhooks.sent', $request);

            return $response;
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            $this->eventDispatcher->fire('webhooks.error', ['request'=>$request, 'error'=>$e->getMessage(), 'httpResponse'=>$e->getResponse()]);

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
