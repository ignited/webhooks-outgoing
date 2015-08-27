<?php
namespace Ignited\Webhooks\Outgoing;

use \GuzzleHttp\Client;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Models\Request;
use Laravel\Lumen\Routing\DispatchesJobs;

/**
 * Class Webhooks
 * @package Ignited\Webhooks\Outgoing
 */
class Webhooks
{
    use DispatchesJobs;

    /**
     * @param $url
     * @param $body
     * @param string $method
     * @return Request
     */
    public function generate($url, $body, $method='post')
    {
        $request = new Request();

        $request->setAttribute('url', $url);
        $request->setAttribute('body', $body);
        $request->setAttribute('method', $method);

        return $request;
    }

    public function dispatch(Request $request)
    {
        $request->save();

        $job = (new WebhookJob($request));

        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function fire(Request $request)
    {
        $client = new Client();

        $response = $client->{$request->getAttribute('method')}($request->getAttribute('url'), [
            'json'=> json_encode($request->getAttribute('body'))
        ]);

        return $response;
    }
}