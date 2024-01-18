<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchApplicationLicenceNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchApplicationLicenceNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchApplicationLicenceNo
    {
        $urlHelper = $container->get('Helper\Url');
        return new SearchApplicationLicenceNo($urlHelper);
    }
}
