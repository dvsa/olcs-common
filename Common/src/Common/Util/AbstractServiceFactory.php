<?php

namespace Common\Util;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return ($this->getClassName($requestedName) !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceClassName = $this->getClassName($requestedName);

        $service = new $serviceClassName();

        if ($service instanceof FactoryInterface) {
            return $service->__invoke($container, $requestedName, $options);
        }

        return $service;
    }

    /**
     * Get the class name from the service name
     *
     * Helper\Access becomes Common\Service\Helper\AccessHelperService
     * Access becomes Common\Service\Access
     *
     * @param string $name Class name
     *
     * @return string
     */
    protected function getClassName($name)
    {
        $namespaces = [
            'Olcs\Service\\',
            'Admin\Service\\',
            'Common\Service\\'
        ];

        if (strstr($name, '\\')) {
            list($type, $name) = explode('\\', $name, 2);

            foreach ($namespaces as $namespace) {
                $className = $namespace . $type . '\\' . $name . $type . 'Service';
                if (class_exists($className)) {
                    return $className;
                }
            }
        }

        return false;
    }
}
