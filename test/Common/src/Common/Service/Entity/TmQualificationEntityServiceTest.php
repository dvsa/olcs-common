<?php

/**
 * TmQualification Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TmQualificationEntityService;

/**
 * TmQualification Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmQualificationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TmQualificationEntityService();

        parent::setUp();
    }

    /**
     * Test get qualifications for TM
     * 
     * @group tmQualificationEntity
     */
    public function testGetQualificationsForTm()
    {
        $id = 1;

        $data = ['transportManager' => $id];
        $initialOrder = [
            ['qualificationType' => ['displayOrder' => 3]],
            ['qualificationType' => ['displayOrder' => 2]],
            ['qualificationType' => ['displayOrder' => 2]],
            ['qualificationType' => ['displayOrder' => 1]]
        ];
        $correctOrder = [
            ['qualificationType' => ['displayOrder' => 1]],
            ['qualificationType' => ['displayOrder' => 2]],
            ['qualificationType' => ['displayOrder' => 2]],
            ['qualificationType' => ['displayOrder' => 3]]
        ];

        $this->expectOneRestCall('TmQualification', 'GET', $data)
            ->will($this->returnValue(['Results' => $initialOrder]));

        $this->assertEquals(['Results' => $correctOrder], $this->sut->getQualificationsForTm($id));
    }

    /**
     * Test get qualification
     * 
     * @group tmQualificationEntity
     */
    public function testGetQualification()
    {
        $id = 1;

        $this->expectOneRestCall('TmQualification', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getQualification($id));
    }
}
