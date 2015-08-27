<?php
namespace Ignited\Webhooks\Outgoing\Jobs;

use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Ignited\Webhooks\Outgoing\Services\RequestServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebhookJob implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $config;
    protected $requests;
    protected $service;

    public function __construct(RequestInterface $request,
                                $config,
                                RequestRepositoryInterface $requests,
                                RequestServiceInterface $service)
    {
        $this->request = $request;
        $this->config = $config;
        $this->requests = $requests;
        $this->service = $service;
    }

    public function handle()
    {
        try {
            $response = $this->service->fire($this->request);

            $this->handleSuccess();
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            $this->handleError($e);
        }
    }

    public function handleSuccess()
    {
        return $this->delete();
    }

    public function handleError(\GuzzleHttp\Exception\RequestException $e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();

            $this->request->response_code = $response->getStatusCode();
        }

        $this->request->attempts += 1;

        $this->requests->update($this->request);

        if($this->request->attempts < $this->config['max_attempts'])
        {
            $seconds = (2 ^ $this->request->attempts);

            $this->release($seconds);
        }

        if($this->request->attempts >= $this->config['max_attempts'])
        {
            $this->delete();
        }
    }
}
