<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchOperatingCentreSelfserveLicNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchOperatingCentreSelfserveLicNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchOperatingCentreSelfserveLicNo
    {
        $translator = $container->get('translator');
        return new SearchOperatingCentreSelfserveLicNo($translator);
    }
}
