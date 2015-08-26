<?php
namespace Ignited\Webhooks\Outgoing;

use \GuzzleHttp\Client;

class Webhooks
{
    public function create($url, $body, $method='post')
    {
        $client = new Client();

        $response = $client->{$method}($url, [
            'json'=> json_encode($body)
        ]);

        return $response;
    }
}