<?php

namespace RateLimiter\LeakyBucket;

use Redis;

/**
 * Implements the leaky bucket storage for Redis storage.
 */
class RedisStorage implements StorageInterface
{
    /**
     * Constructor
     *
     * @param \Redis $redis
     */
    public function __construct(public \Redis $redis)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function attempt($key, int $limit, int $rate): bool
    {
        $luaScript = <<<'LUA'
            local bucket = redis.call("hgetall", KEYS[1])
            local current_time = tonumber(ARGV[1])
            local capacity = tonumber(ARGV[2])
            local leak_rate = tonumber(ARGV[3])

            if next(bucket) == nil then
                bucket["last_time"] = current_time
                bucket["current_water"] = 0
            else
                local last_time = tonumber(bucket[2])
                local current_water = tonumber(bucket[4])

                -- Calculate the amount of water leaked during the time interval
                local time_passed = current_time - last_time
                local leaked_water = time_passed * leak_rate

                -- Update the bucket\'s water level
                current_water = math.max(0, current_water - leaked_water)
                bucket["current_water"] = current_water
                bucket["last_time"] = current_time
            end

            -- Determine if the request can be passed
            if tonumber(bucket["current_water"]) < capacity then
                bucket["current_water"] = tonumber(bucket["current_water"]) + 1
                redis.call("hmset", KEYS[1], "last_time", bucket["last_time"], "current_water", bucket["current_water"])
                return 1
            else
                return 0
            end
        LUA;

        return 1 === $this->redis->eval($luaScript, [$key, time(), $limit, $rate], 1);
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining($key, int $limit, int $rate): int
    {
        $currentTime = time();
        $bucket = $this->redis->hGetAll($key);
        if (!$bucket) {
            return 0;
        }
        $timePassed = $currentTime - $bucket['last_time'];
        $leakedWater = $timePassed * $rate;
        return max(0, $bucket['current_water'] - $leakedWater);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
        $this->redis->del($key);
    }
}
