<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchAddressComplaintFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchAddressComplaint
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressComplaint
    {
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressComplaint($urlHelper);
    }
}
