<?php

declare(strict_types=1);

namespace CommonTest\Controller\Factory\FeatureToggle;

use Common\Test\MockeryTestCase;
use Interop\Container\ContainerInterface;
use Common\Test\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;
use Common\Service\Cqrs\Query\QuerySender;
use Common\Controller\Factory\FeatureToggle\BinaryFeatureToggleAwareControllerFactory;

/**
 * @see BinaryFeatureToggleAwareControllerFactory
 */
class BinaryFeatureToggleAwareControllerFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const SUT_NAME = 'SUT NAME';
    public const ENABLED_FACTORY_RESULT = 'ENABLED FACTORY RESULT';
    public const DISABLED_FACTORY_RESULT = 'DISABLED FACTORY RESULT';
    public const ENABLED_FEATURE_TOGGLE_1 = 'ENABLED FEATURE TOGGLE 1';
    public const ENABLED_FEATURE_TOGGLE_2 = 'ENABLED FEATURE TOGGLE 2';
    public const DISABLED_FEATURE_TOGGLE_1 = 'DISABLED FEATURE TOGGLE 1';
    public const QUERY_SENDER_ALIAS = 'QuerySender';

    /**
     * @var BinaryFeatureToggleAwareControllerFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function __invoke_isCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_isCallable
     */
    public function __invoke_CreatesEnabledService_WhenNoFeatureTogglesAreConfigured()
    {
        // Setup
        $this->setUpSut();
        $pluginManager = $this->setUpAbstractPluginManager($this->setUpServiceManager());

        // Execute
        $result = $this->sut->__invoke($pluginManager, static::SUT_NAME);

        // Assert
        $this->assertEquals(static::ENABLED_FACTORY_RESULT, $result);
    }

    /**
     * @test
     * @depends __invoke_isCallable
     */
    public function __invoke_CreatesEnabledService_WhenSingleFeatureToggleIsEnabled()
    {
        // Setup
        $this->setUpSut([static::ENABLED_FEATURE_TOGGLE_1]);
        $pluginManager = $this->setUpAbstractPluginManager($this->setUpServiceManager());

        // Expect
        $this->serviceManager->get(static::QUERY_SENDER_ALIAS)->expects('featuresEnabled')->with([static::ENABLED_FEATURE_TOGGLE_1])->andReturn(true);

        // Execute
        $result = $this->sut->__invoke($pluginManager, static::SUT_NAME);

        // Assert
        $this->assertEquals(static::ENABLED_FACTORY_RESULT, $result);
    }

    /**
     * @test
     * @depends __invoke_CreatesEnabledService_WhenSingleFeatureToggleIsEnabled
     */
    public function __invoke_ChecksMultipleFeatureTogglesAreEnabled()
    {
        // Setup
        $this->setUpSut([static::ENABLED_FEATURE_TOGGLE_1, static::ENABLED_FEATURE_TOGGLE_2]);
        $pluginManager = $this->setUpAbstractPluginManager($this->setUpServiceManager());

        // Expect
        $this->serviceManager->get(static::QUERY_SENDER_ALIAS)->expects('featuresEnabled')->with([static::ENABLED_FEATURE_TOGGLE_1, static::ENABLED_FEATURE_TOGGLE_2])->andReturn(true);

        // Execute
        $result = $this->sut->__invoke($pluginManager, static::SUT_NAME);

        // Assert
        $this->assertEquals(static::ENABLED_FACTORY_RESULT, $result);
    }

    /**
     * @test
     * @depends __invoke_isCallable
     */
    public function __invoke_CreatesDisabledService_WhenFeatureToggleIsDisabled()
    {
        // Setup
        $this->setUpSut([static::DISABLED_FEATURE_TOGGLE_1]);
        $pluginManager = $this->setUpAbstractPluginManager($this->setUpServiceManager());

        // Expect
        $this->serviceManager->get(static::QUERY_SENDER_ALIAS)->expects('featuresEnabled')->with([static::DISABLED_FEATURE_TOGGLE_1])->andReturn(false);

        // Execute
        $result = $this->sut->__invoke($pluginManager, static::SUT_NAME);

        // Assert
        $this->assertEquals(static::DISABLED_FACTORY_RESULT, $result);
    }

    public function setUpSut(array $featureToggles = [])
    {
        $this->sut = new class($featureToggles) extends BinaryFeatureToggleAwareControllerFactory {

            protected $featureToggles = [];

            public function __construct(array $featureToggles)
            {
                $this->featureToggles = $featureToggles;
            }

            protected function getFeatureToggleNames(): array
            {
                return $this->featureToggles;
            }

            protected function createServiceWhenEnabled(ContainerInterface $container, $requestedName, array $options = null)
            {
                return BinaryFeatureToggleAwareControllerFactoryTest::ENABLED_FACTORY_RESULT;
            }

            protected function createServiceWhenDisabled(ContainerInterface $container, $requestedName, array $options = null)
            {
                return BinaryFeatureToggleAwareControllerFactoryTest::DISABLED_FACTORY_RESULT;
            }
        };
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(static::QUERY_SENDER_ALIAS, $this->setUpMockService(QuerySender::class));
    }
}
