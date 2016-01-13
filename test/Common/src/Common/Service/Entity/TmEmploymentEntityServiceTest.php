<?php

/**
 * TM Employment Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TmEmploymentEntityService;

/**
 * TM Employment Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmEmploymentEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected $dataBundle = [
        'children' => [
            'transportManager',
            'contactDetails' => [
                'children' => [
                    'address'
                ]
            ]
        ]
    ];

    protected function setUp()
    {
        $this->sut = new TmEmploymentEntityService();

        parent::setUp();
    }

    /**
     * Test get all employments for TM
     * 
     * @group tmEmploymentsEntity
     */
    public function testGetAllEmploymentsForTm()
    {
        $this->expectOneRestCall('TmEmployment', 'GET', ['transportManager' => 1], $this->dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllEmploymentsForTm(1));
    }

    /**
     * Test get employment
     * 
     * @group tmEmploymentsEntity
     */
    public function testGetEmployment()
    {
        $this->expectOneRestCall('TmEmployment', 'GET', 1, $this->dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getEmployment(1));
    }
}
