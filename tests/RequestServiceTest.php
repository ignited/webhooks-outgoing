<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ignited\Webhooks\Outgoing\Requests\EloquentRequest;
use Ignited\Webhooks\Outgoing\Services\RequestFailedException;
use Ignited\Webhooks\Outgoing\Services\RequestService;
use Mockery as m;

class RequestServiceTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testDispatch()
    {
        list($service, $requests, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks(m::mock('GuzzleHttp\Client'), ['max_attempts'=>3]);

        $requests->shouldReceive('save')->once()->andReturn(true);

        $dispatcher->shouldReceive('dispatch')->once()->andReturn(true);

        $service->dispatch(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    public function testResetAttempts()
    {
        list($service, $requests, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks();

        $request = new EloquentRequest(['url'=>'http://test.com', 'method'=>'POST', 'body'=>'test', 'attempts'=>1]);

        $requests->shouldReceive('save')->andReturn(true);

        $service->resetAttempts($request);
    }

    public function testSuccessfulFire()
    {
        $mock = new MockHandler([
            new Response(204)
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        list($service, $requests, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks($client);

        $request = new EloquentRequest(['url'=>'http://test.com', 'method'=>'POST', 'body'=>'test', 'attempts'=>1]);

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getStatusCode')->andReturn('200');

        $requests->shouldReceive('save')->with($request)->andReturn(true);

        $eventDispatcher->shouldReceive('fire')->times(2);

        $service->fire($request);
    }

    /**
     * @expectedException     GuzzleHttp\Exception\RequestException
     * @expectedExceptionMessage Client error: 400
     * @expectedExceptionCode 400
     */
    public function testUnsuccessfulFireShouldThrowException()
    {
        $mock = new MockHandler([
            new Response(400)
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        list($service, $requests, $dispatcher, $eventDispatcher, $config) = $this->createWebhooks($client);

        $request = new EloquentRequest(['url'=>'http://test.com', 'method'=>'POST', 'body'=>'test', 'attempts'=>1]);

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getStatusCode')->andReturn(400);

        $requests->shouldReceive('save')->with($request)->andReturn(true);

        $eventDispatcher->shouldReceive('fire')->times(2);

        $service->fire($request);
    }

    protected function createWebhooks($client=null, $config=[])
    {
        $service = new RequestService(
            $requests           = m::mock('Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository'),
            $client ?: m::mock('GuzzleHttp\Client'),
            $dispatcher         = m::mock('Illuminate\Contracts\Bus\Dispatcher'),
            $eventDispatcher    = m::mock('Illuminate\Events\Dispatcher'),
            $config
        );

        return [$service, $requests, $dispatcher, $eventDispatcher, $config];
    }
}
