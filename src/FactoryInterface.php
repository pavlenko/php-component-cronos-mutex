<?php

namespace PE\Component\Cronos\Mutex;

interface FactoryInterface
{
    /**
     * @param string $name
     *
     * @return MutexInterface
     */
    public function create(string $name): MutexInterface;
}
