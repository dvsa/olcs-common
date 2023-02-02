<?php

declare(strict_types=1);

namespace Common\Controller\Factory\FeatureToggle;

use Common\Service\Cqrs\Query\QuerySender;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;

/**
 * A factory that enables developers to create a controller in two different ways depending on whether a  feature toggle
 * has been enabled or disabled.
 *
 * @see \CommonTest\Controller\Factory\FeatureToggle\BinaryFeatureToggleAwareControllerFactoryTest
 */
abstract class BinaryFeatureToggleAwareControllerFactory implements FactoryInterface
{
    /**
     * @return string[]
     */
    abstract protected function getFeatureToggleNames(): array;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    abstract protected function createServiceWhenEnabled(ContainerInterface $container, $requestedName, array $options = null);

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    abstract protected function createServiceWhenDisabled(ContainerInterface $container, $requestedName, array $options = null);

    /**
     * @inheritDoc
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, null, null);
    }

    /**
     * @param ContainerInterface $container
     * @param mixed $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($this->featureTogglesAreEnabled($container, $this->getFeatureToggleNames())) {
            return $this->createServiceWhenEnabled($container, $requestedName, $options);
        }
        return $this->createServiceWhenDisabled($container, $requestedName, $options);
    }

    /**
     * @param ContainerInterface $container
     * @param string[] $featureToggles
     * @return bool
     */
    protected function featureTogglesAreEnabled(ContainerInterface $container, array $featureToggles): bool
    {
        if (empty($featureToggles)) {
            return true;
        }
        $querySender = $container->getServiceLocator()->get('QuerySender');
        assert($querySender instanceof QuerySender, 'Expected instance of QuerySender');
        return $querySender->featuresEnabled($this->getFeatureToggleNames());
    }
}

