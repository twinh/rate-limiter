<?php

namespace RateLimiter\FixedWindow;

/**
 * Interface for storage classes used in the FixedWindow strategy.
 */
interface StorageInterface
{
    /**
     * Increments the value for a given key.
     *
     * @param string $key
     * @return int
     */
    public function incr(string $key): int;

    /**
     * Retrieves the remaining attempts for a given key.
     *
     * @param string $key
     * @return int
     */
    public function getRemaining(string $key): int;

    /**
     * Clears the value for a given key.
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void;
}
