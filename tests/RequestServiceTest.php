<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use Ignited\Webhooks\Outgoing\Services\RequestService;
use Ignited\Webhooks\Outgoing\Webhooks;
use Mockery as m;

class RequestServiceTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function setUp()
    {
        m::mock('Illuminate\Database\Eloquent\Model');
    }

    public function testDispatch()
    {
        list($service, $requests, $client, $dispatcher, $service, $config) = $this->createWebhooks(['max_attempts'=>3]);

        $requests->shouldReceive('save')->once()->andReturn(true);

        $dispatcher->shouldReceive('dispatch')->once()->andReturn(true);

        $service->dispatch(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    public function testFire()
    {
        list($service, $requests, $client, $dispatcher, $service, $config) = $this->createWebhooks();

        $request = m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest');

        $request->shouldReceive('getUrl')->andReturn('http://test.com');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getBody')->andReturn('test');

        $client->shouldReceive('send')->once()->andReturn(true);

        $service->fire($request);
    }

    protected function createWebhooks($config=[])
    {
        $service = new RequestService(
            $requests      = m::mock('Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository'),
            $client        = m::mock('GuzzleHttp\Client'),
            $dispatcher    = m::mock('Illuminate\Contracts\Bus\Dispatcher'),
            $config
        );

        return [$service, $requests, $client, $dispatcher, $service, $config];
    }
}
