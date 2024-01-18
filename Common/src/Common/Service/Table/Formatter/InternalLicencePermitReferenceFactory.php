<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class InternalLicencePermitReferenceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InternalLicencePermitReference
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $urlHelper = $container->get('Helper\Url');
        return new InternalLicencePermitReference($urlHelper);
    }
}
