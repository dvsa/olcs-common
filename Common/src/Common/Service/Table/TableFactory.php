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
    /**
     * Holds the service locator
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Create the table factory service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Common\Service\Table\TableFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Wrap the build table method
     *
     * @param string $name
     * @param array $data
     * @param array $params
     * @param boolean $render
     */
    public function buildTable($name, $data = array(), $params = array(), $render = true)
    {
        $table = new TableBuilder($this->serviceLocator);
        return $table->buildTable($name, $data, $params, $render);
    }
}
