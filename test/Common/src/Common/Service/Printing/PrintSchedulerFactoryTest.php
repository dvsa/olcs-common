<?php

/**
 * Print Scheduler factory test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Printing;

use CommonTest\Bootstrap;
use Common\Service\Printing\PrintSchedulerFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Print Scheduler factory test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PrintSchedulerFactoryTest extends MockeryTestCase
{
    private function createService($config)
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $sut = new PrintSchedulerFactory();

        $this->sm = $sm;

        return $sut->createService($sm);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required print_scheduler configuration
     */
    public function testCreateServiceWithMissingCredentials()
    {
        $this->createService([]);
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing required option print_scheduler.adapter
     */
    public function testCreateServiceWithMissingAdapter()
    {
        $this->createService(
            [
                'print_scheduler' => []
            ]
        );
    }

    public function testCreateServiceWithValidConfig()
    {
        $service = $this->createService(
            [
                'print_scheduler' => [
                    'adapter' => 'DocumentStub'
                ]
            ]
        );

        $this->assertInstanceOf('\Common\Service\Printing\DocumentStubPrintScheduler', $service);

        $this->assertEquals(
            $this->sm,
            $service->getServiceLocator()
        );
    }
}
