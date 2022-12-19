<?php

/**
 * ElasticSearch Factory
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Controller\Plugin;

use Common\Service\Data\Search\Search;
use Common\Service\Data\Search\SearchType;
use Interop\Container\ContainerInterface;
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ElasticSearch
    {
        $plugin = new ElasticSearch();

        $searchService = $container->getServiceLocator()->get('DataServiceManager')->get(Search::class);
        $searchTypeService = $container->getServiceLocator()->get('DataServiceManager')->get(SearchType::class);
        $navigation = $container->getServiceLocator()->get('Navigation');

        $plugin->setSearchService($searchService);
        $plugin->setSearchTypeService($searchTypeService);
        $plugin->setNavigationService($navigation);

        return $plugin;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, ElasticSearch::class);
    }
}
