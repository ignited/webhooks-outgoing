<?php
namespace Ignited\Webhooks\Outgoing\Events;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;

class WebhookEncounteredGuzzleError
{
    public $request;
    public $exception;

    public function __construct(RequestInterface $request, RequestException $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }
}