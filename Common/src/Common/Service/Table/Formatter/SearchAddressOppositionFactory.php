<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchAddressOppositionFactory implements FactoryInterface
{
    /**
     * @param $requestedName
     * @param array|null         $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressOpposition
    {
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressOpposition($urlHelper);
    }
}
