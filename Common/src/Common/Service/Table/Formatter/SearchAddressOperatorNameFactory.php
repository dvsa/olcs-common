<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchAddressOperatorNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchAddressOperatorName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressOperatorName
    {
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressOperatorName($urlHelper);
    }
}
