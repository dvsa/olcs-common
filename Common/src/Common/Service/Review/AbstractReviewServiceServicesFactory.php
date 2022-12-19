<?php

namespace Common\Service\Review;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AbstractReviewServiceServicesFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractReviewServiceServices
    {
        return new AbstractReviewServiceServices(
            $container->get('Helper\Translation')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $services): AbstractReviewServiceServices
    {
        return $this($services, AbstractReviewServiceServices::class);
    }
}
