<?php namespace Likewinter\QueueThrottle;

use Illuminate\Queue\Jobs\BeanstalkdJob;
use Pheanstalk\Pheanstalk;
use Predis\Client;
use Predis\Transaction\MultiExec;

class Throttle
{
    /**
     * @var Pheanstalk
     */
    private $pheanstalk;
    /**
     * @var Client
     */
    private $redis;
    private $limits;
    private $tube;

    /**
     * Throttle constructor.
     * @param Client $redis
     * @param Pheanstalk $pheanstalk
     */
    public function __construct(Client $redis, Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
        $this->redis = $redis;
    }

    /**
     * @param array $limits
     */
    public function setThrottleLimits(array $limits)
    {
        $this->limits = $limits;
    }

    /**
     * @param string $tube
     */
    public function setTube($tube)
    {
        $this->tube = $tube;
    }

    public function setTubeFromJob(BeanstalkdJob $job)
    {
        $stats = $this->pheanstalk->statsJob($job->getPheanstalkJob());
        $this->tube = $stats->tube;
    }

    public function work()
    {
        foreach ($this->limits as $index => $limit) {
            $counterName = "queue:{$this->tube}:throttle:{$index}";
            $started = (bool) $this->redis->setnx($counterName, $limit['requests']);
            $counter = $this->redis->get($counterName);
            if ($started) {
                $this->redis->transaction(function (MultiExec $transaction) use ($counterName, $limit) {
                    $transaction->expire($counterName, $limit['seconds']);
                    $transaction->decr($counterName);
                });
            } else {
                if ($counter > 1) {
                    $this->redis->decr($counterName);
                } else {
                    $this->pheanstalk->pauseTube($this->tube, $this->redis->ttl($counterName));
                }
            }
        }
    }
}
