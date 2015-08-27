<?php

namespace Ignited\Webhooks\Outgoing\Requests;

interface RequestInterface
{
    public function getRequestId();

    public function getUrl();

    public function getMethod();

    public function getBody();
}