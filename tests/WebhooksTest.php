<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use Ignited\Webhooks\Outgoing\Webhooks;
use Mockery as m;

class WebhooksTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function setUp()
    {
        m::mock('Illuminate\Database\Eloquent\Model');
    }

    public function testGenerate()
    {
        list($webhooks, $requests, $client, $dispatcher) = $this->createWebhooks();

        $url = 'http://test.com';
        $body = ['test'=>'go'];

        $requests->shouldReceive('create')->once()->andReturn(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));

        $webhooks->generate($url, $body);
    }

    public function testCreate()
    {
        list($webhooks, $requests, $client, $dispatcher) = $this->createWebhooks();

        $url = 'http://test.com';
        $body = ['test'=>'go'];

        $data = compact(['url', 'body']);

        $requests->shouldReceive('create')->with($data)->once()->andReturn(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));

        $webhooks->create($data);
    }

    public function testDispatch()
    {
        list($webhooks, $requests, $client, $dispatcher) = $this->createWebhooks(['max_attempts'=>3]);

        $requests->shouldReceive('save')->once()->andReturn(true);

        $dispatcher->shouldReceive('dispatch')->once()->andReturn(true);

        $webhooks->dispatch(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    public function testFire()
    {
        list($webhooks, $requests, $client, $dispatcher) = $this->createWebhooks();

        $request = m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest');

        $request->shouldReceive('getUrl')->andReturn('http://test.com');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getBody')->andReturn('test');

        $client->shouldReceive('send')->once()->andReturn(true);

        $webhooks->fire($request);
    }

    protected function createWebhooks($config=[])
    {
        $webhooks = new Webhooks(
            $requests      = m::mock('Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository'),
            $client        = m::mock('GuzzleHttp\Client'),
            $dispatcher    = m::mock('Illuminate\Contracts\Bus\Dispatcher'),
            $config
        );

        return [$webhooks, $requests, $client, $dispatcher];
    }
}
