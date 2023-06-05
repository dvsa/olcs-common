<?php

namespace Common\Filter;

use Interop\Container\ContainerInterface;
use Laminas\Filter\Decompress;
use Laminas\ServiceManager\DelegatorFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DecompressUploadToTmpFactory
 * @package Common\Filter
 */
class DecompressToTmpDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, callable $callback, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $config = $container->get('Config');
        $tmpRoot = ($config['tmpDirectory'] ?? sys_get_temp_dir());
        $filter = new Decompress('zip');

        $service = $callback();
        $service->setDecompressFilter($filter);
        $service->setTempRootDir($tmpRoot);
        $service->setFileSystem($container->get('Common\Filesystem\Filesystem'));

        return $service;
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        return $this($serviceLocator, $requestedName, $callback);
    }
}
