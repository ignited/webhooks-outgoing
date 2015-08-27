<?php
namespace Ignited\Webhooks\Outgoing\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model {

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

}