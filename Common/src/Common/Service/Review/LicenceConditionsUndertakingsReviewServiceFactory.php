<?php

namespace Common\Service\Review;

use Common\Service\Table\Formatter\Address;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LicenceConditionsUndertakingsReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LicenceConditionsUndertakingsReviewService
    {
        return new LicenceConditionsUndertakingsReviewService(
            $container->get(AbstractReviewServiceServices::class),
            $container->get('Review\ConditionsUndertakings'),
            $container->get(FormatterPluginManager::class)->get(Address::class)
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $services): LicenceConditionsUndertakingsReviewService
    {
        return $this($services, LicenceConditionsUndertakingsReviewService::class);
    }
}