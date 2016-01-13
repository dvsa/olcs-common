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
        $bundle = [
            'children' => [
                'case' => [
                    'criteria' => [
                        'application' => 1971,
                    ],
                    'required' => true
                ],
                'oppositionType',
                'opposer' => array(
                    'children' => array(
                        'opposerType',
                        'contactDetails' => array(
                            'children' => array(
                                'person',
                            )
                        )
                    )
                ),
                'grounds',
                'application',
            ]
        ];

        $this->expectOneRestCall(
            'Opposition',
            'GET',
            [
                'sort' => 'createdOn',
                'order' => 'DESC',
                'limit' => 'all'
            ],
            $bundle
        )->will($this->returnValue(['Results' => ['CASES']]));

        $this->assertEquals(['CASES'], $this->sut->getForApplication(1971));
    }

    /**
     * Test getForLicence
     */
    public function testGetForLicence()
    {
        $bundle = [
            'children' => [
                'case' => [
                    'criteria' => [
                        'licence' => 1971,
                    ],
                    'required' => true
                ],
                'oppositionType',
                'opposer' => array(
                    'children' => array(
                        'opposerType',
                        'contactDetails' => array(
                            'children' => array(
                                'person',
                            )
                        )
                    )
                ),
                'grounds',
                'application',
            ]
        ];

        $this->expectOneRestCall(
            'Opposition',
            'GET',
            [
                'sort' => 'createdOn',
                'order' => 'DESC',
                'limit' => 'all'
            ],
            $bundle
        )->will($this->returnValue(['Results' => ['CASES']]));

        $this->assertEquals(['CASES'], $this->sut->getForLicence(1971));
    }
}
