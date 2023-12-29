<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class BusRegNumberLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return BusRegNumberLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator = $container->get('translator');
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $container->get('Helper\Url');
        return new BusRegNumberLink($translator, $viewHelperManager, $urlHelper);
    }
}
