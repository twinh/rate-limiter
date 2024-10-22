<?php

namespace RateLimiter\FixedWindow;

/**
 * Implements the StorageInterface for in-memory storage.
 */
class MemoryStorage implements StorageInterface
{
    /**
     * Holds the data for the memory storage.
     *
     * @var array<int>
     */
    protected array $data = [];

    /**
     * {@inheritDoc}
     */
    public function incr(string $key): int
    {
        $this->data[$key] = $this->data[$key] ?? 0;
        return ++$this->data[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining(string $key): int
    {
        return (int) ($this->data[$key] ?? 0);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($key): void
    {
        unset($this->data[$key]);
    }
}
