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

// Create storage
/* @var $storage StorageInterface */
$storage = '...';

// Create mutex
$mutex = new Mutex('SOME_KEY', $storage);

// Execute critical section via callable and timeout as second argument:
$mutex->synchronize(function () {
    // Do some stuff...
}, 1);

// TODO disallow next code possibility
// Or execute critical section via manual acquire/release calls
if ($mutex->acquireLock()) {
    // Do some stuff
    // Then you must manually release lock
    $mutex->releaseLock();
}
```
