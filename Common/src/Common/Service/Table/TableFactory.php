<?php
namespace Common\Service\Table;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Table Factory
 * Creates an instance of TableBuilder and passes in the application config
 *
 * @deprecated See: olcs-common/Common/config/module.config.php, line: 273
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
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator
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
     *
     * @return TableBuilder
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
