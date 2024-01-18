<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TmApplicationManagerTypeFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TmApplicationManagerType
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $application = $container->get('Application');
        $urlHelper = $container->get('Helper\Url');
        $translator = $container->get('translator');
        return new TmApplicationManagerType($application, $urlHelper, $translator);
    }
}
