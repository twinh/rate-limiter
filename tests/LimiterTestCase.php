<?php

namespace RateLimiterTest;

use PHPUnit\Framework\TestCase;

class LimiterTestCase extends TestCase
{
    protected static function createRedis(): \Redis
    {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST') ?: 'localhost', getenv('REDIS_PORT') ?: 6379);
        $password = getenv('REDIS_PASSWORD');
        if ($password) {
            $redis->auth($password);
        }
        return $redis;
    }
}
