<?php

namespace Common\Service\Data\Search;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return Search
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Search
    {
        $sl = $container->getServiceLocator();

        return new Search(
            $sl->get('Table'),
            $sl->get('ViewHelperManager'),
            $sl->get(SearchTypeManager::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return Search
     */
    public function createService(ServiceLocatorInterface $services): Search
    {
        return $this($services, Search::class);
    }
}
