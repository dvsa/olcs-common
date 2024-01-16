<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchBusRegSelfserveFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchBusRegSelfserve
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchBusRegSelfserve
    {
        $urlHelper = $container->get('Helper\Url');
        $translator = $container->get('translator');
        return new SearchBusRegSelfserve($urlHelper, $translator);
    }
}
