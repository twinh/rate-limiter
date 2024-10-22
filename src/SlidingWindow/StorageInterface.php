<?php

namespace RateLimiter\SlidingWindow;

/**
 * Interface for storage classes used in the SlidingWindow strategy.
 */
interface StorageInterface
{
    /**
     * Attempts to execute a rate-limited operation
     *
     * @param string $key
     * @param int $limit
     * @param int $windowSize
     * @return bool
     */
    public function attempt(string $key, int $limit, int $windowSize): bool;

    /**
     * Retrieves the remaining attempts for a given key.
     *
     * @param string $key
     * @param int $windowSize
     * @return int
     */
    public function getRemaining(string $key, int $windowSize): int;

    /**
     * Clears the value for a given key.
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void;
}
