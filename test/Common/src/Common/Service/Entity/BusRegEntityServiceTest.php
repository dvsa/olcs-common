<?php

/**
 * Bus Registration Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\BusRegEntityService;

/**
 * Bus Registration Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusRegEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new BusRegEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                'RESPONSE'
            ]
        ];
        $this->expectOneRestCall('BusReg', 'GET', ['regNo' => 123])
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $this->expectOneRestCall('BusReg', 'GET', ['regNo' => 123])
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findByIdentifier(123));
    }
}
