<?php

namespace RateLimiterTest;

use RateLimiter\SlidingWindow;
use RateLimiter\SlidingWindow\MemoryStorage;
use RateLimiter\SlidingWindow\RedisStorage;
use RateLimiter\SlidingWindow\StorageInterface;

class SlidingWindowTest extends LimiterTestCase
{
    /**
     * @dataProvider providerForStorage
     */
    public function testAttempt(StorageInterface $storage): void
    {
        $limiter = new SlidingWindow($storage, 2, 2);
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
        $limiter = new SlidingWindow($storage, 10, 10);
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
        $limiter = new SlidingWindow(new MemoryStorage(), 10);
        $this->assertSame(10, $limiter->getWindowSize());
    }

    public function testSetWindowSize(): void
    {
        $limiter = new SlidingWindow(new MemoryStorage(), 10);
        $limiter->setWindowSize(20);
        $this->assertSame(20, $limiter->getWindowSize());
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
