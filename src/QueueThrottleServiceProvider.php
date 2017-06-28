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
            $redisHost = 'tcp://' . env('REDIS_HOST', '127.0.0.1') . ':6379';
            $beanstalkdHost = env('BEANSTALKD_HOST', '127.0.0.1');

            return new Throttle(
                new RedisClient($redisHost),
                new Pheanstalk($beanstalkdHost)
            );
        });
    }
}
