<?php

namespace Common\Filesystem;

use Symfony\Component\Filesystem\Filesystem as BaseFileSystem;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Wraps PHP shell functions
 *
 * @codeCoverageIgnore
 */
class Shell
{
    /**
     * Execute a system command
     *
     * @param string $command Command to execute
     * @param array  &$output Reference variable, if present will contain command output
     *
     * @return int $result
     */
    public function execute($command, &$output = null)
    {
        $output = null;
        $result = null;
        exec($command, $output, $result);

        return $result;
    }
}
