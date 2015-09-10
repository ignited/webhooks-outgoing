<?php

return [

    'requests' => [
        'model' => env('WEBHOOKS_REQUESTS_MODEL', 'Ignited\Webhooks\Outgoing\Requests\EloquentRequest'),
    ],


    // Here you can define the queue name to use for webhook processing
    'queue_name' => env('WEBHOOKS_QUEUE_NAME', 'default'),

    'max_attempts' => env('WEBHOOKS_MAX_ATTEMPTS', 19),

];
