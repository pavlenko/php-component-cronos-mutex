## Requirements

The following versions of PHP are supported.

* PHP 7.1+

## Installation

To install, use composer:

```
php composer.phar require pe/component-cronos-mutex
```

## Usage

```php
<?php

namespace PE\Component\Cronos;

use PE\Component\Cronos\Mutex\Storage\StorageInterface;
use PE\Component\Cronos\Mutex\Mutex;
use PE\Component\Cronos\Mutex\Synchronize;
use PE\Component\Cronos\Mutex\Waiting;

// Create storage
/* @var $storage StorageInterface */
$storage = '...';

// Create mutex
$mutex = new Mutex('SOME_KEY', $storage);

// You can use wrapper for wait lock released
$mutex = new Waiting($mutex, 1000);

// Typically execute logic
if ($mutex->acquireLock()) {
    // Do some stuff ...
    $mutex->releaseLock();
}

// You can use synchronized wrapper for automatically acquire/release lock
$synchronize = new Synchronize($mutex);
$synchronize->execute(function () {
    // Do some stuff ...
});
```
