<?php

/**
 * Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use Common\Service\Processing\EntityProcessingService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class EntityProcessingServiceTest extends MockeryTestCase
{
    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new EntityProcessingService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testFindEntityForCategory()
    {
        $licenceMock = m::mock()
            ->shouldReceive('findByIdentifier')
            ->with(123)
            ->andReturn('FakeEntity')
            ->getMock();

        $this->sm->setService('Entity\Licence', $licenceMock);

        $this->assertEquals(
            'FakeEntity',
            $this->sut->findEntityForCategory(
                1,
                123
            )
        );
    }
}
