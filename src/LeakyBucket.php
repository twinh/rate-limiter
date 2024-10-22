<?php

declare(strict_types=1);

namespace RateLimiter;

use RateLimiter\LeakyBucket\StorageInterface;

/**
 * Leaky Bucket rate limiter
 */
class LeakyBucket extends AbstractRateLimiter
{
    /**
     * The storage interface used to manage the rate limit counters.
     *
     * @var StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * The rate at which the bucket leaks
     *
     * @var int
     */
    protected int $rate;

    /**
     * Constructor
     *
     * @param StorageInterface $storage
     * @param int $rate
     * @param int|null $limit
     */
    public function __construct(StorageInterface $storage, int $rate, ?int $limit = null)
    {
        $this->storage = $storage;
        $this->rate = $rate;
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     */
    public function attempt(?string $key = null, ?int $limit = null): bool
    {
        return $this->storage->attempt($this->buildKey($key), $limit ?? $this->limit, $this->rate);
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining(?string $key = null, ?int $limit = null): int
    {
        $limit = $limit ?? $this->limit;
        return $limit - $this->storage->getRemaining($this->buildKey($key), $this->limit, $this->rate);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(?string $key = null): void
    {
        $this->storage->clear($this->buildKey($key));
    }

    /**
     * Set the leaky rate
     *
     * @param int $rate
     * @return $this
     */
    public function setRate(int $rate): static
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * Get the leaky rate
     *
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

    /**
     * Generates a storage key for the given key.
     *
     * @param string|null $key
     * @return string
     */
    protected function buildKey(?string $key = null): string
    {
        return 'leaky-bucket:' . ($key ?? 'global');
    }
}
