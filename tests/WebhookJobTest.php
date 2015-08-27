<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use Mockery as m;

class WebhookJobTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function setUp()
    {

    }

    public function testJobFires()
    {
        list($job, $request, $config) = $this->createWebhookJob();

        $service = m::mock('Ignited\Webhooks\Outgoing\Services\RequestService');
        $service->shouldReceive('fire')->once();

        $job->shouldReceive('delete')->times(1);

        $job->handle($service);
    }

    public function testBackOffExponential()
    {
        list($job, $request, $config) = $this->createWebhookJob(['max_attempts'=>3]);
        $exception = m::mock('GuzzleHttp\Exception\RequestException');

        $request->shouldReceive('getAttribute')
            ->andReturn(2);

        $service = m::mock('Ignited\Webhooks\Outgoing\Services\RequestService');

        $service->shouldReceive('fire')->once()->andThrow($exception);
        $service->shouldReceive('getDelayInSeconds')->once()->andReturn(10);

        $job->shouldReceive('release')->with(10)->times(1);

        $job->handle($service);
    }

    public function testJobFailsOnMaxAttempts()
    {
        list($job, $request, $config) = $this->createWebhookJob(['max_attempts'=>3]);

        $request->shouldReceive('getAttribute')
            ->andReturn(3);

        $exception = m::mock('GuzzleHttp\Exception\RequestException');

        $service = m::mock('Ignited\Webhooks\Outgoing\Services\RequestService');

        $service->shouldReceive('fire')->once()->andThrow($exception);

        $job->shouldReceive('release')->never();
        $job->shouldReceive('delete')->once();

        $job->handle($service);
    }

    protected function createWebhookJob($config=[])
    {
        $job = m::mock('Ignited\Webhooks\Outgoing\Jobs\WebhookJob', [
            $request     = m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'),
            $config
        ])->makePartial();

        return [$job, $request, $config];
    }
}
