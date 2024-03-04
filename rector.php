<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Common',
        //__DIR__ . '/Scripts',
        // __DIR__ . '/test',
    ])
    ->withPhpSets();
