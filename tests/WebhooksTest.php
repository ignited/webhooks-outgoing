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
        list($webhooks, $requests, $service) = $this->createWebhooks();

        $url = 'http://test.com';
        $body = ['test'=>'go'];

        $requests->shouldReceive('create')->once()->andReturn(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));

        $webhooks->generate($url, $body);
    }

    public function testCreate()
    {
        list($webhooks, $requests, $service) = $this->createWebhooks();

        $url = 'http://test.com';
        $body = ['test'=>'go'];

        $data = compact(['url', 'body']);

        $requests->shouldReceive('create')->with($data)->once()->andReturn(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));

        $webhooks->create($data);
    }

    public function testDispatch()
    {
        list($webhooks, $requests, $service) = $this->createWebhooks(['max_attempts'=>3]);

        $service->shouldReceive('dispatch')->once();

        $webhooks->dispatch(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    public function testFire()
    {
        list($webhooks, $requests, $service) = $this->createWebhooks();

        $service->shouldReceive('fire')->once();

        $webhooks->fire(m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'));
    }

    protected function createWebhooks($config=[])
    {
        $webhooks = new Webhooks(
            $requests      = m::mock('Ignited\Webhooks\Outgoing\Requests\IlluminateRequestRepository'),
            $service       = m::mock('Ignited\Webhooks\Outgoing\Services\RequestService')
        );

        return [$webhooks, $requests, $service];
    }
}
