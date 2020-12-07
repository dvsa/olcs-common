<?php

/**
 * ElasticSearch Factory
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Controller\Plugin;

use Common\Service\Data\Search\Search;
use Common\Service\Data\Search\SearchType;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Mvc\Controller\Plugin\PluginInterface;

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
        $navigation = $serviceLocator->getServiceLocator()->get('Navigation');

        $plugin->setSearchService($searchService);
        $plugin->setSearchTypeService($searchTypeService);
        $plugin->setNavigationService($navigation);

        return $plugin;
    }
}
