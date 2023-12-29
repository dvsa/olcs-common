<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class NumberStackValueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NumberStackValue
    {
        $stackHelper = $container->get('Helper\Stack');
        return new NumberStackValue($stackHelper);
    }
}
