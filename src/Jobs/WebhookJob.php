<?php
namespace Ignited\Webhooks\Outgoing\Jobs;

use Ignited\Webhooks\Outgoing\Facades\Webhooks;
use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
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

    public function __construct(RequestInterface $request,
                                $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    public function handle()
    {
        try {
            $response = Webhooks::fire($this->request);

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

        Webhooks::update($this->request);

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
