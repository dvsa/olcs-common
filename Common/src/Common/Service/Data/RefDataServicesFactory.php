<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;


/**
 * RefDataServicesFactory
 */
class RefDataServicesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return RefDataServices
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefDataServices
    {
        return new RefDataServices(
            $container->get(AbstractListDataServiceServices::class),
            $container->get('LanguagePreference')
        );
    }
}
