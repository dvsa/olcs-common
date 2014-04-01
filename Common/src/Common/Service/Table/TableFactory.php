<?php

/**
 * Table Factory
 *
 * Creates an instance of TableBuilder and passes in the application config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Table Factory
 *
 * Creates an instance of TableBuilder and passes in the application config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TableBuilder($serviceLocator->get('Config'));
    }

}
