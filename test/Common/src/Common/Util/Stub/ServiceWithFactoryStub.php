<?php

namespace CommonTest\Common\Util\Stub;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ServiceWithFactoryStub implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ServiceWithFactoryStub
    {
        return $this;
    }
}
