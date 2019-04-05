<?php

namespace PE\Component\Cronos\Mutex;

use Predis\Client;

class LockRedis implements LockInterface
{
    /**
     * @var Client|\Redis
     */
    private $client;

    /**
     * @var string
     */
    private $name;

    /**
     * @param Client|\Redis $client
     * @param string        $name
     */
    public function __construct($client, string $name)
    {
        if (!($client instanceof Client) && !($client instanceof \Redis)) {
            throw new \InvalidArgumentException();
        }

        $this->client = $client;
        $this->name   = $name;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(): bool
    {
        return (bool) $this->client->setnx($this->name, '1');
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(): bool
    {
        return (bool) $this->client->del($this->name);
    }

    /**
     * @inheritDoc
     */
    public function containLock(): bool
    {
        return (bool) $this->client->get($this->name);
    }
}
