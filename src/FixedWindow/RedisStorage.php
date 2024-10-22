<?php

namespace RateLimiter\FixedWindow;

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
    public function incr(string $key): int
    {
        return $this->redis->incrBy($key, 1);
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining($key): int
    {
        return (int) $this->redis->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
        $this->redis->del($key);
    }
}
