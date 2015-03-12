<?php

/**
 * Creates configured instances of GenericCrudController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Filter\Word\CamelCaseToDash;

/**
 * Creates configured instances of GenericCrudController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericCrudControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $serviceName = null, $requestedName = null)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $crudServiceName = $this->getCrudServiceName($requestedName);
        $translationPrefix = $this->getTranslationPrefix($requestedName);

        /** @var \Common\Service\Crud\AbstractCrudService $service */
        $service = $mainServiceLocator->get('CrudServiceManager')->get($crudServiceName);

        /** @var \Common\Controller\Crud\GenericCrudController $controller */
        $controller = $serviceLocator->get('GenericCrudController');
        $controller->setCrudService($service);
        $controller->setTranslationPrefix($translationPrefix);

        /**
         * Set config options
         *
         * This is a the crud_controller_config array from the module.config.php file.
         */
        $config = $mainServiceLocator->get('Config');
        if (isset($config['crud_controller_config'][$requestedName])) {
            $options = $config['crud_controller_config'][$requestedName];

            $controller->setOptions($options);
        }

        /**
         * Set scripts
         *
         * Sets the inline java scripts as an event just prior to dispatch.
         * Also sets up the required parameters
         */
        $controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, [$controller, 'setUpParams'], 100);
        $controller->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, [$controller, 'setUpScripts'], 10000);

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
