<?php
namespace Ignited\Webhooks\Outgoing\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/15
 * Time: 8:45 AM
 */
class Webhooks extends Facade
{
    protected static function getFacadeAccessor() { return 'webhooks'; }
}