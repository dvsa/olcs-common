<?php

/**
 * TrafficArea Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TrafficAreaEntityService;

/**
 * TrafficArea Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TrafficAreaEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage No traffic area value options found
     */
    public function testGetValueOptionsWithoutValueOptions()
    {
        $response = array(
            'Results' => array()
        );

        $this->expectOneRestCall('TrafficArea', 'GET', array())
            ->will($this->returnValue($response));

        $this->sut->getValueOptions();
    }

    /**
     * @group entity_services
     */
    public function testGetValueOptions()
    {
        $response = array(
            'Results' => array(
                array(
                    'id' => 'A',
                    'name' => 'c'
                ),
                array(
                    'id' => 'B',
                    'name' => 'a'
                ),
                array(
                    'id' => 'C',
                    'name' => 'b'
                ),
                array(
                    'id' => 'N',
                    'name' => 'a'
                )
            )
        );

        $expected = array(
            'B' => 'a',
            'C' => 'b',
            'A' => 'c'
        );

        $this->expectOneRestCall('TrafficArea', 'GET', array())
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getValueOptions());
    }
}
