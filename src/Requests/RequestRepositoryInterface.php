<?php

namespace Ignited\Webhooks\Outgoing\Requests;

interface RequestRepositoryInterface
{
    public function findById($id);
}