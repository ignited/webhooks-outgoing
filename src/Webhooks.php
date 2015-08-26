<?php
namespace Ignited\Webhooks\Outgoing;

use \GuzzleHttp\Client;

/**
 * Class Webhooks
 * @package Ignited\Webhooks\Outgoing
 */
class Webhooks
{
    /**
     * @param $url
     * @param $body
     * @return Request
     */
    public function generate($url, $body)
    {
        $request = new Request();
        $request->setUrl($url);
        $request->setBody($body);

        return $request;
    }

    public function dispatch(Request $request)
    {
        return $this->fire($request);
    }

    public function fire(Request $request)
    {
        $client = new Client();

        $response = $client->{$request->getMethod()}($request->getURL(), [
            'json'=> json_encode($request->getBody())
        ]);

        return $response;
    }
}