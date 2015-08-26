### Laravel Webhooks (Outgoing)
Simple `Queue` driven webhook library.


#### Synchronous (no queues)
```php
$request = Webhooks::generate();
$response = Webhooks::fire($request);
````

#### Asynchronous (queue based, backoff, etc)
```php
$request = Webhooks::generate();
Webhooks::dispatch($request);
````

`Note:` you will need to run an instance of `php artisan queue:listen` to ensure queues are delivered.

Todo List:
- [ ] Define failed attempts
- [ ] Backoff