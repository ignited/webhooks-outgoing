<?php
namespace Ignited\Webhooks\Outgoing\Requests;

use Illuminate\Database\Eloquent\Model;

class EloquentRequest extends Model implements RequestInterface {

    protected $table = 'requests';

    protected $fillable = [
        'url',
        'method',
        'body',
        'response_code',
        'attempts'
    ];

    protected $casts = [
        'body' => 'json'
    ];

    public function getRequestId()
    {
        return $this->id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getMethod()
    {
        return $this->method;
    }

}