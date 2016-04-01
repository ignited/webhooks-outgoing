<?php
namespace Ignited\Webhooks\Outgoing;

use GuzzleHttp\ClientInterface;
use Ignited\Webhooks\Outgoing\Jobs\WebhookJob;
use Ignited\Webhooks\Outgoing\Models\Request;
use Ignited\Webhooks\Outgoing\Requests\RequestInterface;
use Ignited\Webhooks\Outgoing\Requests\RequestRepositoryInterface;
use Ignited\Webhooks\Outgoing\Services\DispatchInterface;
use Ignited\Webhooks\Outgoing\Services\RequestService;
use Illuminate\Contracts\Bus\Dispatcher;

/**
 * Class Webhooks
 * @package Ignited\Webhooks\Outgoing
 */
class Webhooks
{
    protected $requests;
    protected $service;
    protected $config;

    public function __construct(RequestRepositoryInterface $requests,
                                RequestService $service
    )
    {
        $this->requests = $requests;
        $this->service = $service;
    }

    public function generate($url, $body, $method='post')
    {
        $request = $this->requests->create(compact(['url', 'body', 'method']));

        return $request;
    }

    public function generateExt($url, $body, $params=[])
    {
        if(!isset($params) || !isset($params['method'])){
            $params['method'] = 'post';
        }

        $request = $this->requests->create(array_merge(compact(['url', 'body']), $params));

        return $request;
    }

    public function create($data)
    {
        $request = $this->requests->create($data);

        return $request;
    }

    public function reset(RequestInterface $request)
    {
        return $this->service->resetAttempts($request);
    }

    public function delete(RequestInterface $request)
    {
        return $this->requests->delete($request);
    }

    public function update(RequestInterface $request)
    {
        return $this->requests->save($request);
    }

    public function dispatch(RequestInterface $request)
    {
        return $this->service->dispatch($request);
    }

    public function fire(RequestInterface $request)
    {
        return $this->service->fire($request);
    }
}