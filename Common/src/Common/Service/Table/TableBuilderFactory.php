<?php

namespace Common\Service\Table;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Laminas Framework Compatible Table Builder Factory. Creates an instance of
 * TableBuilder and passes in the main service locator
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TableBuilderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TableBuilder(
            $container,
            $container->get('ZfcRbac\Service\AuthorizationService'),
            $container->get('translator'),
            $container->get('Helper\Url'),
            $container->get('Config')
        );
    }

    /**
     * Create the table factory service, and returns TableBuilder. A
     * true Laminas Framework Compatible Table Builder Factory.
     *
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, TableBuilder::class);
    }
}
