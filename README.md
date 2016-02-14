# Laravel beanstalkd queue throttle

## Installing

### Laravel:
Register service provider by adding in ```config/app.php```
```php
'providers' => [
    // Other Service Providers
    
    Likewinter\QueueThrottle\QueueThrottleServiceProvider::class
],
```

### Lumen
Register service provider by adding in ```bootstrap/app.php```
```php
$app->register(Likewinter\QueueThrottle\QueueThrottleServiceProvider::class);
```

## Using:
Inside your Job class add trait and set limits
```php
use CanLimitRate;

protected $rateLimits = [
    ['requests' => 10, 'seconds' => 10],
    ['requests' => 15, 'seconds' => 30],
];
```
and at the begining of ```handle()``` method
```php
$this->throttle();
```
