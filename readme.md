# PACKAGE STILL IN DEVELOPMENT
## Laravel Webhooks (Outgoing)

[![Build Status](https://travis-ci.org/ignited/webhooks-outgoing.svg?branch=master)](https://travis-ci.org/ignited/webhooks-outgoing)
[![Total Downloads](https://poser.pugx.org/ignited/webhooks-outgoing/d/total.svg)](https://packagist.org/packages/rydurham/sentinel)


Simple `Queue` driven webhook interface. Just send your webhook url, body (and method) and let the library take care of the rest. Supports asynchronous (backed by Laravel queues) to ensure message delivery.

### Synchronous
No queues - simple to setup but no delivery assurance
```php
$request = Webhooks::generate($url, $body, $method);
$response = Webhooks::fire($request);
````

### Asynchronous (easy to use)
Requires queues - but implements backoff, x failed attempts, and retry etc.
```php
$request = Webhooks::generate($url, $body, $method);
Webhooks::dispatch($request);
````

`Note:` you will need to run an instance of `php artisan queue:listen` to ensure queues are delivered.

Todo List:
- [ ] Define failed attempts
- [ ] Backoff
- [ ] Exception handling for Synchronous Webhooks
