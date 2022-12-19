<?php
namespace Common\Service\Table;

use Interop\Container\ContainerInterface;
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
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->serviceLocator = $container;
        return $this;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, TableFactory::class);
    }

    /**
     * Get an instance of table builder
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTableBuilder()
    {
        $tableBuilderFactory = new TableBuilderFactory();
        return $tableBuilderFactory($this->serviceLocator, TableBuilder::class);
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
