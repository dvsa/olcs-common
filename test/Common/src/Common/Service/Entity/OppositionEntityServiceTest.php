<?php

/**
 * Opposition Entity Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OppositionEntityService;

/**
 * Opposition Entity Service Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OppositionEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OppositionEntityService();

        parent::setUp();
    }

    /**
     * Test getForApplication
     */
    public function testGetForApplication()
    {
        $this->expectOneRestCall(
            'Opposition',
            'GET',
            [
                'application' => 1971,
                'sort' => 'createdOn',
                'order' => 'DESC',
                'limit' => 'all'
            ]
        )->will($this->returnValue(['Results' => ['CASES']]));

        $this->assertEquals(['CASES'], $this->sut->getForApplication(1971));
    }

    /**
     * Test getForLicence
     */
    public function testGetForLicence()
    {
        $this->expectOneRestCall(
            'Opposition',
            'GET',
            [
                'licence' => 1971,
                'sort' => 'createdOn',
                'order' => 'DESC',
                'limit' => 'all'
            ]
        )->will($this->returnValue(['Results' => ['CASES']]));

        $this->assertEquals(['CASES'], $this->sut->getForLicence(1971));
    }
}
