<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 10:38 AM
 */

namespace Ignited\Webhooks\Outgoing\Requests;

use Ignited\Webhooks\Outgoing\Traits\RepositoryTrait;

class IlluminateRequestRepository implements RequestRepositoryInterface
{
    use RepositoryTrait;

    protected $model;

    public function __construct($model = null)
    {
        if (isset($model)) {
            $this->model = $model;
        }
    }

    public function create($data)
    {
        $request = $this->createModel($data);

        return $request;
    }

    public function save($request)
    {
        return $request->save();
    }

    public function findById($id)
    {
        return $this
            ->createModel()
            ->newQuery()
            ->find($id);
    }
}