<?php

namespace Common\Service\Review;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LicenceConditionsUndertakingsReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LicenceConditionsUndertakingsReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ConditionsUndertakings')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return LicenceConditionsUndertakingsReviewService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, LicenceConditionsUndertakingsReviewService::class);
    }
}
