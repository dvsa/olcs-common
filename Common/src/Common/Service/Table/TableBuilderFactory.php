<?php
namespace Common\Service\Table;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Zend Framework Compatible Table Builder Factory. Creates an instance of
 * TableBuilder and passes in the main service locator
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TableBuilderFactory implements FactoryInterface
{
    /**
     * Create the table factory service, and returns TableBuilder. A
     * true Zend Framework Compatible Table Builder Factory.
     *
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableBuilder =  new TableBuilder($serviceLocator);

        /** @var \Laminas\Mvc\I18n\Translator $translator */
        $translator  = $serviceLocator->get('translator');
        $tableBuilder->setTranslator($translator);

        return $tableBuilder;
    }
}
