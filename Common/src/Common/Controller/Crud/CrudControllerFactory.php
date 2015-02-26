<?php

/**
 * Crud Controller Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Filter\Word\CamelCaseToDash;

/**
 * Crud Controller Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $serviceName = null, $requestedName = null)
    {
        $crudServiceName = $this->getCrudServiceName($requestedName);
        $translationPrefix = $this->getTranslationPrefix($requestedName);

        $service = $serviceLocator->getServiceLocator()->get('CrudServiceManager')->get($crudServiceName);

        $controller = $serviceLocator->get('GenericCrudController');
        $controller->setCrudService($service);
        $controller->setTranslationPrefix($translationPrefix);

        return $controller;
    }

    protected function getCrudServiceName($requestedName)
    {
        $parts = explode('\\', $requestedName);
        $lastPart = array_pop($parts);

        return str_replace('Controller', 'CrudService', $lastPart);
    }

    protected function getTranslationPrefix($requestedName)
    {
        $parts = explode('\\', $requestedName);
        $lastPart = array_pop($parts);
        $importantPart = str_replace('Controller', '', $lastPart);

        $filter = new CamelCaseToDash();
        return 'crud-' . strtolower($filter->filter($importantPart));
    }
}
