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
     * Get an instance of table builder
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTableBuilder()
    {
        return new TableBuilder($this->serviceLocator);
    }

    /**
     * Wrap the prepare table method
     *
     * @param string $name
     * @param array $data
     * @param array $params
     */
    public function prepareTable($name, array $data = array(), array $params = array())
    {
        return $this->getTableBuilder()->prepareTable($name, $data, $params);
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
        $table = $this->getTableBuilder();
        return $table->buildTable($name, $data, $params, $render);
    }
}
