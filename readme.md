## Laravel Webhooks (Outgoing)
Simple `Queue` driven webhook library.


### Synchronous
No queues - simple to setup but no delivery assurance
```php
$request = Webhooks::generate();
$response = Webhooks::fire($request);
````

### Asynchronous (easy to use)
Requires queues - but implements backoff, x failed attempts, and retry etc.
```php
$request = Webhooks::generate();
Webhooks::dispatch($request);
````

`Note:` you will need to run an instance of `php artisan queue:listen` to ensure queues are delivered.

Todo List:
- [ ] Define failed attempts
- [ ] Backoff
- [ ] Exception handling for Synchronous Webhooks