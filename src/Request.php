<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 9:01 AM
 */

namespace Ignited\Webhooks\Outgoing;

class Request
{
    protected $method = 'post';
    protected $url;
    protected $body;

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function setURL($url)
    {
        $this->url = $url;
    }
}