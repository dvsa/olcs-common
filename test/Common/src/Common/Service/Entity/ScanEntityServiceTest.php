<?php

/**
 * Scan Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ScanEntityService;

/**
 * Scan Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScanEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ScanEntityService();

        parent::setUp();
    }

    public function testFindById()
    {
        $response = array('RESPONSE');

        $this->expectOneRestCall('Scan', 'GET', 1)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->findById(1));
    }

    public function testGetChildRelations()
    {
        $this->assertEquals(
            ['licence', 'busReg', 'case', 'transportManager', 'category', 'subCategory'],
            $this->sut->getChildRelations()
        );
    }
}
