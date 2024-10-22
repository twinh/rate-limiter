<?php

namespace RateLimiter;

interface RateLimiterInterface
{
    /**
     * Attempts to execute a rate-limited operation
     *
     * @param string|null $key The key to use for rate limiting
     * @param int|null $limit The maximum number of attempts
     * @return bool
     */
    public function attempt(?string $key = null, ?int $limit = null): bool;

    /**
     * Retrieves the remaining attempts for a given key.
     *
     * @param string|null $key
     * @param int|null $limit
     * @return int
     */
    public function getRemaining(?string $key = null, ?int $limit = null): int;

    /**
     * Clears the rate limit for a given key.
     *
     * @param string|null $key
     * @return void
     */
    public function clear(?string $key = null): void;
}
