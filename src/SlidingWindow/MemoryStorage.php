<?php

namespace RateLimiter\SlidingWindow;

/**
 * Implements the StorageInterface for in-memory storage.
 */
class MemoryStorage implements StorageInterface
{
    /**
     * Holds the data for the memory storage.
     *
     * @var array<array<int>>
     */
    protected array $data = [];

    /**
     * {@inheritDoc}
     */
    public function attempt(string $key, int $limit, int $windowSize): bool
    {
        $this->filterExpired($key, $windowSize);
        if (count($this->data[$key]) < $limit) {
            $this->data[$key][] = time();
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining(string $key, int $windowSize): int
    {
        $this->filterExpired($key, $windowSize);
        return count($this->data[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Filters out expired timestamps from the data array.
     *
     * @param string $key
     * @param int $windowSize
     * @return void
     */
    protected function filterExpired(string $key, int $windowSize): void
    {
        $time = time();
        $this->data[$key] = array_filter(
            $this->data[$key] ?? [],
            static function ($timestamp) use ($time, $windowSize) {
                return $timestamp > $time - $windowSize;
            }
        );
    }
}
