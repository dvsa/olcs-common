<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Decompress;
use Laminas\Filter\Exception;
use Common\Filesystem\Filesystem;

/**
 * Class DecompressToTmp
 * @package Common\Filter
 */
class DecompressToTmp extends AbstractFilter
{
    protected Decompress $decompressFilter;
    protected string $tempRootDir;
    protected Filesystem $fileSystem;

    public function setDecompressFilter(Decompress $decompressFilter): static
    {
        $this->decompressFilter = $decompressFilter;
        return $this;
    }

    public function getDecompressFilter(): Decompress
    {
        return $this->decompressFilter;
    }

    public function setTempRootDir(string $tempRootDir): static
    {
        $this->tempRootDir = $tempRootDir;
        return $this;
    }

    public function getTempRootDir(): string
    {
        return $this->tempRootDir;
    }

    public function setFileSystem(Filesystem $fileSystem): static
    {
        $this->fileSystem = $fileSystem;
        return $this;
    }

    public function getFileSystem(): Filesystem
    {
        return $this->fileSystem;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     */
    public function filter($value): string
    {
        $tmpDir = $this->createTmpDir();

        $adapterOptions = $this->getDecompressFilter()->getAdapterOptions();
        $adapterOptions['target'] = $tmpDir;

        $this->getDecompressFilter()->setAdapterOptions($adapterOptions);
        return $this->getDecompressFilter()->filter($value);
    }


    /**
     * Creates temp directory for extracting to, registers shutdown function to remove it at script end
     *
     * @return string
     */
    protected function createTmpDir(): string
    {
        $filesystem = $this->getFileSystem();
        $tmpDir = $filesystem->createTmpDir($this->getTempRootDir(), 'zip');

        register_shutdown_function(
            static function () use ($tmpDir, $filesystem) {
                $filesystem->remove($tmpDir);
            }
        );

        return $tmpDir;
    }
}
