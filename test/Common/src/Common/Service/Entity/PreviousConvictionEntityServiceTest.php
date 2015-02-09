<?php

/**
 * PreviousConviction Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PreviousConvictionEntityService;

/**
 * PreviousConviction Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousConvictionEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PreviousConvictionEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetDataForApplication()
    {
        $id = 7;

        $data = array('application' => $id);
        $expected = array('foo');
        $response = array('Results' => $expected);

        $this->expectOneRestCall('PreviousConviction', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getDataForApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetData()
    {
        $id = 7;

        $this->expectOneRestCall('PreviousConviction', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForTransportManager()
    {
        $id = 7;

        $this->expectOneRestCall('PreviousConviction', 'GET', ['transportManager' => $id])
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getDataForTransportManager($id));
    }
}
