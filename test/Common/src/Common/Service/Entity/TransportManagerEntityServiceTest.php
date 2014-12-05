<?php

/**
 * TransportManager Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TransportManagerEntityService;

/**
 * TransportManager Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TransportManagerEntityService();

        parent::setUp();
    }

    /**
     * @group transportManagerEntity
     */
    public function testGetTmDetails()
    {
        $this->expectOneRestCall('TransportManager', 'GET', 1)
            ->will($this->returnValue([]));

        $this->assertEquals([], $this->sut->getTmDetails(1));
    }
}
