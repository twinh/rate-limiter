<?php

namespace RateLimiter\SlidingWindow;

use Redis;

/**
 * Implements the StorageInterface for Redis storage.
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
    public function attempt(string $key, int $limit, int $windowSize): bool
    {
        $luaScript = <<<'LUA'
            local current_time = tonumber(ARGV[1])
            local value = ARGV[2]
            local limit = tonumber(ARGV[3])
            local window_size = tonumber(ARGV[4])

            -- Delete expired items
            redis.call('zremrangebyscore', KEYS[1], 0, current_time - window_size)

            -- If the number of keys is less than the limit, add a new key
            local number = redis.call('zcard', KEYS[1]);
            if number < limit then
                redis.call('zadd', KEYS[1], current_time, value)
                redis.call('expire', KEYS[1], window_size + 1)
                return 1
            else
                return 0
            end
        LUA;

        return 1 === $this->redis->eval($luaScript, [$key, time(), uniqid(), $limit, $windowSize], 1);
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining($key, int $windowSize): int
    {
        // Delete expired items
        $this->redis->zRemRangeByScore($key, '0', (string) (time() - $windowSize));

        // Return the number of remaining items
        return $this->redis->zCard($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
        $this->redis->del($key);
    }
}
