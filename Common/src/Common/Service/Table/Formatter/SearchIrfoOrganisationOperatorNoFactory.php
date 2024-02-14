<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchIrfoOrganisationOperatorNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchIrfoOrganisationOperatorNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchIrfoOrganisationOperatorNo
    {
        $urlHelper = $container->get('Helper\Url');
        return new SearchIrfoOrganisationOperatorNo($urlHelper);
    }
}
