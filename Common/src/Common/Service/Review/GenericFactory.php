<?php

namespace Common\Service\Review;

use Common\Service\Table\Formatter\Address;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Common\Service\Traits\GenericFactoryCreateServiceTrait;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;

class GenericFactory implements FactoryInterface
{
    use GenericFactoryCreateServiceTrait;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName(
            $container->get(AbstractReviewServiceServices::class),
            $container->get(FormatterPluginManager::class)->get(Address::class)
        );
    }
}
