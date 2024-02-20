<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StackValueFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return StackValue
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $stackHelper = $container->get('Helper\Stack');
        return new StackValue($stackHelper);
    }
}
