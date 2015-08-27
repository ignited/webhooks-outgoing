<?php

namespace Ignited\Webhooks\Outgoing\Requests;

interface RequestRepositoryInterface
{
    public function create($data);

    public function findById($id);
}