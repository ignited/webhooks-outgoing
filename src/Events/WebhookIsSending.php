<?php
namespace Ignited\Webhooks\Outgoing\Events;

use Ignited\Webhooks\Outgoing\Requests\RequestInterface;

class WebhookIsSending
{
    public $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
}