<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TaskDescriptionFactory implements FactoryInterface
{
    /**
     * @param  $requestedName
     * @param  array|null         $options
     * @return TaskDescription
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $router = $container->get('Router');
        $request = $container->get('Request');
        $urlHelper = $container->get('Helper\Url');
        return new TaskDescription($router, $request, $urlHelper);
    }
}
