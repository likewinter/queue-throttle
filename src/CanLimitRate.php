<?php namespace Likewinter\QueueThrottle;

trait CanLimitRate
{

    protected function throttle()
    {
        /** @var Throttle $throttle */
        $throttle = app()->make('Likewinter\\QueueThrottle\\Throttle');
        $throttle->setThrottleLimits($this->rateLimits);
        $throttle->setTubeFromJob($this->job);
        $throttle->work();
    }
}
