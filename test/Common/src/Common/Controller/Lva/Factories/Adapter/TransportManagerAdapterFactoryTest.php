<?php

namespace CommonTest\Controller\Lva\Factories;

use Common\Controller\Lva\Adapters;
use Common\Controller\Lva\Factories\Adapter as AdapterFactory;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Common\Controller\Lva\Factories\Adapter\AbstractTransportManagerAdapterFactory
 * @covers Common\Controller\Lva\Factories\Adapter\ApplicationTransportManagerAdapterFactory
 * @covers Common\Controller\Lva\Factories\Adapter\VariationTransportManagerAdapterFactory
 * @covers Common\Controller\Lva\Factories\Adapter\LicenceTransportManagerAdapterFactory
 */
class TransportManagerAdapterFactoryTest extends MockeryTestCase
{
    /** @var ServiceManager */
    protected $sm;

    public function setUp()
    {
        $this->sm = $this->createMock(ServiceLocatorInterface::class);
        $this->sm->expects(static::exactly(3))
            ->method('get')
            ->willReturnMap(
                [
                    ['TransferAnnotationBuilder', $this->createMock(AnnotationBuilder::class)],
                    ['QueryService', $this->createMock(CachingQueryService::class, [], [], '', false)],
                    ['CommandService', $this->createMock(CommandService::class, [], [], '', false)],
                ]
            );
    }

    public function testCreateServiceApplication()
    {
        $factory = new AdapterFactory\ApplicationTransportManagerAdapterFactory();

        static::assertInstanceOf(
            Adapters\ApplicationTransportManagerAdapter::class,
            $factory->createService($this->sm)
        );
    }

    public function testCreateServiceLicence()
    {
        $factory = new AdapterFactory\LicenceTransportManagerAdapterFactory();

        static::assertInstanceOf(
            Adapters\LicenceTransportManagerAdapter::class,
            $factory->createService($this->sm)
        );
    }

    public function testCreateServiceVariation()
    {
        $factory = new AdapterFactory\VariationTransportManagerAdapterFactory();

        static::assertInstanceOf(
            Adapters\VariationTransportManagerAdapter::class,
            $factory->createService($this->sm)
        );
    }
}
