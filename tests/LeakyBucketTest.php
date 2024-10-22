<?php

namespace RateLimiterTest;

use RateLimiter\LeakyBucket;
use RateLimiter\LeakyBucket\MemoryStorage;
use RateLimiter\LeakyBucket\RedisStorage;
use RateLimiter\LeakyBucket\StorageInterface;

class LeakyBucketTest extends LimiterTestCase
{
    /**
     * @dataProvider providerForStorage
     */
    public function testAttempt(StorageInterface $storage): void
    {
        $limiter = new LeakyBucket($storage, 2, 2);
        $limiter->clear('key');

        $this->assertTrue($limiter->attempt('key'));
        $this->assertTrue($limiter->attempt('key'));
        $this->assertFalse($limiter->attempt('key'));

        $limiter->clear('key');
    }

    /**
     * @dataProvider providerForStorage
     */
    public function testRemaining(StorageInterface $storage): void
    {
        $limiter = new LeakyBucket($storage, 10, 10);
        $limiter->clear('key');

        $this->assertSame(10, $limiter->getRemaining('key'));

        $this->assertTrue($limiter->attempt('key'));
        $this->assertSame(9, $limiter->getRemaining('key'));

        $this->assertTrue($limiter->attempt('key'));
        $this->assertSame(8, $limiter->getRemaining('key'));

        $limiter->clear('key');
    }

    public function testGetWindowSize(): void
    {
        $limiter = new LeakyBucket(new MemoryStorage(), 10);
        $this->assertSame(10, $limiter->getRate());
    }

    public function testSetWindowSize(): void
    {
        $limiter = new LeakyBucket(new MemoryStorage(), 10);
        $limiter->setRate(20);
        $this->assertSame(20, $limiter->getRate());
    }

    protected static function providerForStorage(): array
    {
        return [
            [
                new RedisStorage(static::createRedis()),
            ],
            [
                new MemoryStorage(),
            ],
        ];
    }
}
