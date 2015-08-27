<?php
namespace Ignited\Webhooks\Outgoing\Events;

use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class WebhookWasSent
{
    public $request;

    public $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}