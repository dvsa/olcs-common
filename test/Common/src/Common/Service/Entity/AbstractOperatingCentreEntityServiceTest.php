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

    public function testGetOperatingCentreListForLva()
    {
        $id = 4;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', ['' => $id, 'limit' => 'all'])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getOperatingCentreListForLva($id));
    }

    public function testGetListForLva()
    {
        $id = 2;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', ['' => $id, 'limit' => 'all'])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getListForLva($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAddressSummaryData()
    {
        $id = 7;

        $this->setEntity('Foo');
        $this->setProperty('type', 'bar');

        $expectedParams = array(
            'bar' => $id,
            'limit' => 'all',
            'sort' => 'operatingCentre'
        );

        $this->expectOneRestCall('Foo', 'GET', $expectedParams)
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
