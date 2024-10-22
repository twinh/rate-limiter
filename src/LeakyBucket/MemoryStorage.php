<?php

namespace RateLimiter\LeakyBucket;

/**
 * Implements the leaky bucket storage for in-memory storage.
 */
class MemoryStorage implements StorageInterface
{
    /**
     * Holds the data for the memory storage.
     *
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * {@inheritDoc}
     */
    public function attempt(string $key, int $limit, int $rate): bool
    {
        $currentTime = time();
        $data = $this->data[$key] ?? ['lastTime' => 0, 'water' => 0];

        $timePassed = $currentTime - $data['lastTime'];
        $data['lastTime'] = $currentTime;

        // Calculate the leaked water
        $data['water'] = max(0, $data['water'] - $timePassed * $rate);

        if ($data['water'] < $limit) {
            ++$data['water'];
            $this->data[$key] = $data;
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getRemaining($key, int $limit, int $rate): int
    {
        $data = $this->data[$key] ?? ['lastTime' => 0, 'water' => 0];

        $timePassed = time() - $data['lastTime'];
        $data['water'] = max(0, $data['water'] - $timePassed * $rate);

        return $data['water'];
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
        unset($this->data[$key]);
    }
}
