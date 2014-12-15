<?php

/**
 * Print Scheduler factory
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Printing;

use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Print Scheduler factory
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PrintSchedulerFactory implements FactoryInterface
{
    /**
     * Holds the service locator
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Get the instance of the factory
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['print_scheduler'])) {
            throw new RuntimeException('Missing required print_scheduler configuration');
        }

        $config = $config['print_scheduler'];

        if (!isset($config['adapter'])) {
            throw new RuntimeException('Missing required option print_scheduler.adapter');
        }

        $className = __NAMESPACE__ . '\\' . $config['adapter'] . 'PrintScheduler';

        $instance = new $className();
        $instance->setServiceLocator($serviceLocator);

        return $instance;
    }
}
