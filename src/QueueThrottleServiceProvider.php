<?php namespace Likewinter\QueueThrottle;

use Illuminate\Support\ServiceProvider;
use Pheanstalk\Pheanstalk;
use Predis\Client as RedisClient;

class QueueThrottleServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Likewinter\QueueThrottle\Throttle', function () {
            return new Throttle(new RedisClient, new Pheanstalk('127.0.0.1'));
        });
    }
}
