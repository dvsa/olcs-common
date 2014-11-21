<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;
use Common\Filesystem\Filesystem;

/**
 * Class DecompressToTmp
 * @package Common\Filter
 */
class DecompressToTmp extends AbstractFilter
{
    /**
     * @var \Zend\Filter\Decompress
     */
    protected $decompressFilter;

    /**
     * @var string
     */
    protected $tempRootDir;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @param \Zend\Filter\Decompress $decompressFilter
     * @return $this
     */
    public function setDecompressFilter($decompressFilter)
    {
        $this->decompressFilter = $decompressFilter;
        return $this;
    }

    /**
     * @return \Zend\Filter\Decompress
     */
    public function getDecompressFilter()
    {
        return $this->decompressFilter;
    }

    /**
     * @param string $tempRootDir
     * @return $this
     */
    public function setTempRootDir($tempRootDir)
    {
        $this->tempRootDir = $tempRootDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getTempRootDir()
    {
        return $this->tempRootDir;
    }

    /**
     * @param FileSystem $fileSystem
     * @return $this
     */
    public function setFileSystem($fileSystem)
    {
        $this->fileSystem = $fileSystem;
        return $this;
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $tmpDir = $this->createTmpDir();

        $this->getDecompressFilter()->setOptions(['options' => ['target' => $tmpDir]]);
        return $this->getDecompressFilter()->filter($value);
    }


    /**
     * Creates temp directory for extracting to, registers shutdown function to remove it at script end
     *
     * @return string
     */
    protected function createTmpDir()
    {
        $filesystem = $this->getFileSystem();
        $tmpDir = $filesystem->createTmpDir($this->getTempRootDir(), 'zip');

        register_shutdown_function(
            function () use ($tmpDir, $filesystem) {
                $filesystem->remove($tmpDir);
            }
        );

        return $tmpDir;
    }
}
