<?php

namespace Common\Service\Data\Search;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        return new Search(
            $container->get('Table'),
            $container->get('ViewHelperManager'),
            $container->get(SearchTypeManager::class)
        );
    }
}
