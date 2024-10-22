<?php

namespace RateLimiter;

abstract class AbstractRateLimiter implements RateLimiterInterface
{
    /**
     * The number of items allowed in the limiter
     */
    protected ?int $limit;

    /**
     * Set the number of items allowed in the limiter
     *
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the number of items allowed in the limiter
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
