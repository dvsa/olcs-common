<?php

namespace Common\Filter\Publication\Builder;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\Input;

/**
 * Class PublicationBuilderAbstractFactory
 */
class PublicationBuilderAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');
        return isset($config['publications'][$requestedName]);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return \Zend\Filter\FilterChain
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        $input = new Input();

        $filterChain = $input->getFilterChain();

        if (isset($config['publications'][$requestedName])) {
            foreach ($config['publications'][$requestedName] as $filter) {
                $newFilter = $serviceLocator->get('FilterManager')->get($filter);
                $newFilter->setServiceLocator($serviceLocator->get('DataServiceManager'));
                $filterChain->attach($newFilter);
                $filterChain->getPluginManager()->setInvokableClass($filter, $filter);
            }
        }

        return $filterChain;
    }
}
