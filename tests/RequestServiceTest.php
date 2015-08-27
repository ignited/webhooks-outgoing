<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use Ignited\Webhooks\Outgoing\Requests\EloquentRequest;
use Ignited\Webhooks\Outgoing\Services\RequestFailedException;
use Ignited\Webhooks\Outgoing\Services\RequestService;
use Mockery as m;

class RequestServiceTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testDispatch()
    {
        list($service, $requests, $client, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks(['max_attempts'=>3]);

        $requests->shouldReceive('save')->once()->andReturn(true);

        $dispatcher->shouldReceive('dispatch')->once()->andReturn(true);

        $service->dispatch(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    public function testSuccessfulFire()
    {
        list($service, $requests, $client, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks();

        $request = new EloquentRequest(['url'=>'http://test.com', 'method'=>'POST', 'body'=>'test', 'attempts'=>1]);

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getStatusCode')->andReturn('200');

        $requests->shouldReceive('save')->with($request)->andReturn(true);

        $client->shouldReceive('send')->once()->andReturn($response);

        $eventDispatcher->shouldReceive('fire')->times(2);

        $service->fire($request);
    }

    /**
     * @expectedException     GuzzleHttp\Exception\RequestException
     * @expectedExceptionMessage Could Not Contact Server
     * @expectedExceptionCode 400
     */
    public function testUnsuccessfulFireShouldThrowException()
    {
        list($service, $requests, $client, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks();

        $request = new EloquentRequest(['url'=>'http://test.com', 'method'=>'POST', 'body'=>'test', 'attempts'=>1]);

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getStatusCode')->andReturn(400);

        $exception = new \GuzzleHttp\Exception\RequestException('Could Not Contact Server', m::mock('Psr\Http\Message\RequestInterface'), $response);

        $client->shouldReceive('send')->once()->andThrow($exception);

        $requests->shouldReceive('save')->with($request)->andReturn(true);

        $eventDispatcher->shouldReceive('fire')->times(2);

        $service->fire($request);
    }

    protected function createWebhooks($config=[])
    {
        $service = new RequestService(
            $requests           = m::mock('Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository'),
            $client             = m::mock('GuzzleHttp\Client'),
            $dispatcher         = m::mock('Illuminate\Contracts\Bus\Dispatcher'),
            $eventDispatcher    = m::mock('Illuminate\Events\Dispatcher'),
            $config
        );

        return [$service, $requests, $client, $dispatcher, $eventDispatcher, $config];
    }
}
