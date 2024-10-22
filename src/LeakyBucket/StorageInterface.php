<?php

namespace RateLimiter\LeakyBucket;

/**
 * Interface for storage classes used in the LeakyBucket strategy.
 */
interface StorageInterface
{
    /**
     * Attempts to execute a rate-limited operation.
     *
     * @param string $key
     * @param int $limit
     * @param int $rate
     * @return bool
     */
    public function attempt(string $key, int $limit, int $rate): bool;

    /**
     * Retrieves the remaining attempts for a given key.
     *
     * @param string $key
     * @param int $limit
     * @param int $rate
     * @return int
     */
    public function getRemaining(string $key, int $limit, int $rate): int;

    /**
     * Clears the value for a given key.
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void;
}
