<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AddressFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return Address
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dataHelper = $container->get('Helper\Data');
        return new Address($dataHelper);
    }
}
