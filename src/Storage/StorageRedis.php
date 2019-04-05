<?php

namespace PE\Component\Cronos\Mutex\Storage;

use Predis\Client;

final class StorageRedis extends StorageBase
{
    /**
     * @var Client|\Redis
     */
    private $client;

    /**
     * @param Client|\Redis $client
     */
    public function __construct($client)
    {
        if (!($client instanceof Client) && !($client instanceof \Redis)) {
            throw new \InvalidArgumentException();
        }

        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        return (bool) $this->client->setnx($name, '1');
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        return (bool) $this->client->del($name);
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        return (bool) $this->client->exists($name);
    }
}
