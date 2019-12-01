<?php

namespace Common\Service\Data;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class BundleManager
 * @package Common\Service\Data
 */
class BundleManager extends AbstractPluginManager implements AbstractFactoryInterface
{
    protected $instanceOf = Bundle::class;

    public function __construct()
    {
        parent::__construct();
        $this->addAbstractFactory($this);
        $this->addInitializer(array($this, 'initBundle'));
    }

    public function initBundle($instance, $serviceLocator)
    {
        if ($instance instanceof Bundle) {
            $instance->init($serviceLocator);
        }
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return true;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ($this->has('Olcs\Data\Object\Bundle\\' . $requestedName, false, false) ||
            ($this->autoAddInvokableClass && class_exists('Olcs\Data\Object\Bundle\\' . $requestedName))
        ) {
            $this->setInvokableClass(
                'Olcs\Data\Object\Bundle\\' . $requestedName,
                'Olcs\Data\Object\Bundle\\' . $requestedName
            );
            return $this->get('Olcs\Data\Object\Bundle\\' . $requestedName);
        }

        if ($this->has('Common\Data\Object\Bundle\\' . $requestedName, false, false) ||
            ($this->autoAddInvokableClass && class_exists('Common\Data\Object\Bundle\\' . $requestedName))
        ) {
            $this->setInvokableClass(
                'Common\Data\Object\Bundle\\' . $requestedName,
                'Common\Data\Object\Bundle\\' . $requestedName
            );
            return $this->get('Common\Data\Object\Bundle\\' . $requestedName);
        }

        return new Bundle();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($instance),
                $this->instanceOf
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
