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

    /**
     * @group entity_services
     */
    public function testFindByIdentifier()
    {
        $this->expectOneRestCall('TransportManager', 'GET', 123)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group transportManagerEntity
     */
    public function testGetDocuments()
    {
        $bundle = [
            'children' => [
                'documents' => [
                    'children' => [
                        'category',
                        'subCategory'
                    ],
                    'criteria' => [
                        'category'    => 3,
                        'subCategory' => 4,
                        'application' => 2,
                    ]
                ]
            ]
        ];
        $this->expectOneRestCall('TransportManager', 'GET', 1, $bundle)
            ->will($this->returnValue(['documents' => ['documents']]));
        $this->assertEquals(['documents'], $this->sut->getDocuments(1, 2, 'application', 3, 4));
    }
}
