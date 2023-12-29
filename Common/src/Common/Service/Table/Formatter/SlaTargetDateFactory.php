<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SlaTargetDateFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SlaTargetDate
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dateFormatter = $container->get(Date::class);
        $router = $container->get('Router');
        $request = $container->get('Request');
        $urlHelper = $container->get('Helper\Url');
        return new SlaTargetDate($router, $request, $urlHelper, $dateFormatter);
    }
}
