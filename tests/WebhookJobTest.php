<?php
namespace Ignited\Webhooks\Outgoing\Tests;

use Ignited\Webhooks\Outgoing\Facades\Webhooks;
use Mockery as m;

class WebhookJobTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function setUp()
    {
        m::mock('Illuminate\Database\Eloquent\Model');
    }

    public function testJobFires()
    {
        list($job, $request) = $this->createWebhookJob();

        Webhooks::shouldReceive('fire')->once();

        $job->shouldReceive('delete')->times(1);

        $job->handle();
    }

    public function testBackOffExponential()
    {
        list($job, $request) = $this->createWebhookJob(['max_attempts'=>3]);

        $request->attempts = 0;

        $exception = m::mock('GuzzleHttp\Exception\RequestException');

        $exception->shouldReceive('hasResponse')->andReturn(true);
        $exception->shouldReceive('getResponse')->andReturn($response = m::mock('Psr\Http\Message\ResponseInterface'));
        $response->shouldReceive('getStatusCode')->andReturn(404);

        Webhooks::shouldReceive('update')->once()->andReturn(true);

        Webhooks::shouldReceive('fire')->once()->andThrow($exception);

        $seconds = (2 ^ $request->attempts+1);

        $job->shouldReceive('release')->with($seconds)->times(1);

        $job->handle();
    }

    public function testJobFailsOnMaxAttempts()
    {
        list($job, $request) = $this->createWebhookJob(['max_attempts'=>3]);

        $request->attempts = 3;

        $exception = m::mock('GuzzleHttp\Exception\RequestException');

        $exception->shouldReceive('hasResponse')->andReturn(true);
        $exception->shouldReceive('getResponse')->andReturn($response = m::mock('Psr\Http\Message\ResponseInterface'));
        $response->shouldReceive('getStatusCode')->andReturn(404);

        Webhooks::shouldReceive('update')->once()->andReturn(true);

        Webhooks::shouldReceive('fire')->once()->andThrow($exception);

        $job->shouldReceive('release')->never();
        $job->shouldReceive('delete')->once();

        $job->handle();
    }

    protected function createWebhookJob($config=[])
    {
        $job = m::mock('Ignited\Webhooks\Outgoing\Jobs\WebhookJob', [
            $request     = m::mock('Ignited\Webhooks\Outgoing\Requests\EloquentRequest'),
            $config
        ])->makePartial();

        return [$job, $request];
    }
}
