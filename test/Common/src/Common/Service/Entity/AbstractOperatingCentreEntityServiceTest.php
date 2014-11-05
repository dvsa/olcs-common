<?php

/**
 * AbstractOperatingCentre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

/**
 * AbstractOperatingCentre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractOperatingCentreEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass('\Common\Service\Entity\AbstractOperatingCentreEntityService');

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetAddressSummaryData()
    {
        $id = 7;

        $this->setEntity('Foo');
        $this->setProperty('type', 'bar');

        $this->expectOneRestCall('Foo', 'GET', array('bar' => $id))
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAddressSummaryData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAddressData()
    {
        $id = 7;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAddressData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOperatingCentresCount()
    {
        $id = 7;

        $this->setEntity('Foo');
        $this->setProperty('type', 'bar');

        $this->expectOneRestCall('Foo', 'GET', array('bar' => $id))
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getOperatingCentresCount($id));
    }
}
