<?php

namespace RateLimiter;

use RateLimiter\SlidingWindow\StorageInterface;

/**
 * Sliding Window rate limiter
 */
class SlidingWindow extends AbstractRateLimiter
{
    /**
     * The storage interface used to manage the rate limit counters.
     *
     * @var StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * The size of the window in seconds
     */
    protected int $windowSize;

    /**
     * Constructor
     *
     * @param StorageInterface $storage
     * @param int $windowSize
     * @param int|null $limit
     */
    public function __construct(StorageInterface $storage, int $windowSize, ?int $limit = null)
    {
        $this->storage = $storage;
        $this->windowSize = $windowSize;
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     */
    public function attempt(?string $key = null, ?int $limit = null): bool
    {
        return $this->storage->attempt($this->buildKey($key), $limit ?? $this->limit, $this->windowSize);
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining(?string $key = null, ?int $limit = null): int
    {
        return ($limit ?? $this->limit) - $this->storage->getRemaining($this->buildKey($key), $this->windowSize);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(?string $key = null): void
    {
        $this->storage->clear($this->buildKey($key));
    }

    /**
     * Returns the size of the window in seconds.
     *
     * @return int
     */
    public function getWindowSize(): int
    {
        return $this->windowSize;
    }

    /**
     * Sets the size of the window in seconds.
     *
     * @param int $windowSize
     */
    public function setWindowSize(int $windowSize): void
    {
        $this->windowSize = $windowSize;
    }

    /**
     * Generates a storage key for the given key.
     *
     * @param string|null $key
     * @return string
     */
    protected function buildKey(?string $key = null): string
    {
        return 'sliding_window:' . ($key ?? 'global');
    }
}
