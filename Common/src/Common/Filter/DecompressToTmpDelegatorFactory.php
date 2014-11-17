<?php

namespace Common\Filter;

use Zend\Filter\Decompress;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DecompressUploadToTmpFactory
 * @package Common\Filter
 */
class DecompressToTmpDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $tmpRoot = (isset($config['tmpDirectory']) ? $config['tmpDirectory'] : sys_get_temp_dir());
        $filter = new Decompress('zip');

        $service = $callback();
        $service->setDecompressFilter($filter);
        $service->setTempRootDir($tmpRoot);
        $service->setFileSystem($serviceLocator->getServiceLocator()->get('Common\Filesystem\Filesystem'));

        return $service;
    }
}
