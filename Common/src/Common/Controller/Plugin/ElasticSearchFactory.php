<?php

/**
 * ElasticSearch Factory
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Controller\Plugin;

use Common\Service\Data\Search\Search;
use Olcs\Service\Data\Search\SearchType;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\Plugin\PluginInterface;

/**
 * ElasticSearch Factory
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ElasticSearchFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugin = new ElasticSearch();

        $searchService = $serviceLocator->getServiceLocator()->get('DataServiceManager')->get(Search::class);
        $searchTypeService = $serviceLocator->getServiceLocator()->get('DataServiceManager')->get(SearchType::class);

        $plugin->setSearchService($searchService);
        $plugin->setSearchTypeService($searchTypeService);

        return $plugin;
    }
}
