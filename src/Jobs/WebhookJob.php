<?php
namespace Ignited\Webhooks\Outgoing\Jobs;

use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
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

    public function __construct(RequestInterface $request,
                                $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    public function handle(RequestServiceInterface $service)
    {
        try {
            $service->fire($this->request);
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            if($this->request->attempts < $this->config['max_attempts'])
            {
                $seconds = $service->getDelayInSeconds($this->request);

                return $this->release($seconds);
            }
        }

        return $this->delete();
    }
}
