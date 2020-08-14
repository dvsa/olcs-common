<?php

namespace Common\Filesystem;

use Symfony\Component\Filesystem\Filesystem as BaseFileSystem;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;


/**
 * Class Filesystem
 * @package Common\Filesystem
 */
class Filesystem extends BaseFileSystem
{
    const MAX_LOCK_ATTEMPTS = 3;

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public function createTmpDir(string $path, string $prefix = ''): string
    {
        $lock = $this->getLock($path);
        $this->acquireLock($lock);

        do {
            $dirname = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($this->exists($dirname));

        $this->mkdir($dirname);

        $lock->release();

        return $dirname;
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public function createTmpFile(string $path, string $prefix = ''): string
    {
        $lock = $this->getLock($path);
        $this->acquireLock($lock);

        do {
            $filename = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($this->exists($filename));

        $this->touch($filename);

        $lock->release();

        return $filename;
    }


    /**
     * @param LockInterface $lock
     * @param bool $blocking
     * @param int $maxAttempts
     * @throws LockConflictedException | LockAcquiringException
     */
    private function acquireLock(LockInterface $lock, bool $blocking = true, int $maxAttempts = self::MAX_LOCK_ATTEMPTS): void
    {
        for ($currentAttempt = 1; $currentAttempt <= $maxAttempts; $currentAttempt++)
        {
            try {
                $lock->acquire($blocking);
                break;
            } catch (LockConflictedException | LockAcquiringException $exception) {
                if ($currentAttempt >= $maxAttempts) {
                    throw $exception;
                }
                usleep(500);
            }
        }
    }

    /**
     * @param string $path
     * @return LockInterface
     */
    protected function getLock(string $path): LockInterface
    {
        $store = new FlockStore();
        $factory = new LockFactory($store);
        return $factory->createLock($path);
    }
}
