<?php

/**
 * Variation Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter;

/**
 * Variation Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $mockService;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationConditionsUndertakingsAdapter();

        $this->sut->setServiceLocator($this->sm);

        $this->mockService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $this->mockService);
    }

    public function testGetTableName()
    {
        $this->assertEquals('lva-variation-conditions-undertakings', $this->sut->getTableName());
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();
        $this->sm->setService('Script', $mockScript);

        $mockScript->shouldReceive('loadFile')
            ->with('lva-crud-delta');

        $this->sut->attachMainScripts();
    }

    public function testRestoreWithoutAction()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'A'
            ],
            [
                'id' => 456,
                'action' => 'E'
            ]
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->assertFalse($this->sut->restore(123, $parentId));
        $this->assertFalse($this->sut->restore(456, $parentId));
    }

    public function testRestoreWithDelete()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'U'
            ],
            [
                'id' => 456,
                'action' => 'D'
            ]
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->mockService->shouldReceive('delete')
            ->with(123)
            ->shouldReceive('delete')
            ->with(456);

        $this->assertTrue($this->sut->restore(123, $parentId));
        $this->assertTrue($this->sut->restore(456, $parentId));
    }

    public function testRestoreWithDeleteChild()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'C',
                'variationRecords' => [
                    [
                        'id' => 654
                    ]
                ]
            ]
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->mockService->shouldReceive('delete')
            ->with(654);

        $this->assertTrue($this->sut->restore(123, $parentId));
    }

    /**
     * @dataProvider providerCanEditRecord
     */
    public function testCanEditRecord($stubbedCondition, $expected)
    {
        $id = 123;
        $parentId = 321;

        $this->mockGetConditionForVariation($id, $parentId, $stubbedCondition);

        $this->assertEquals($expected, $this->sut->canEditRecord($id, $parentId));
    }

    /**
     * @dataProvider providerDetermineAction
     */
    public function testDetermineAction($stubbedCondition, $expected)
    {
        $id = 123;
        $parentId = 321;

        $this->mockGetConditionForVariation($id, $parentId, $stubbedCondition);

        $this->assertEquals($expected, $this->sut->determineAction($id, $parentId));
        // Assert twice to test caching
        $this->assertEquals($expected, $this->sut->determineAction($id, $parentId));
    }

    /**
     * @dataProvider providerGetTableDataEmpty
     */
    public function testGetTableData($stubbedData, $expected)
    {
        $id = 123;

        $this->mockGetForVariation($id, $stubbedData);

        $this->assertEquals($expected, $this->sut->getTableData($id));
        // Assert twice to test caching
        $this->assertEquals($expected, $this->sut->getTableData($id));
    }

    public function testSaveAdd()
    {
        $data = [
            'foo' => 'bar'
        ];

        $expectedData = [
            'foo' => 'bar',
            'action' => 'A',
            'addedVia' => ConditionUndertakingEntityService::ADDED_VIA_APPLICATION
        ];

        // Expectations
        $this->mockService->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 123]);

        $this->assertEquals(123, $this->sut->save($data));
    }

    /**
     * @dataProvider providerSaveEdit
     */
    public function testSaveEdit($action)
    {
        $data = [
            'id' => 123,
            'application' => 321
        ];

        // AEU (Only actions we can edit)
        $stubbedCondition = [
            'id' => 123,
            'action' => $action
        ];

        $this->mockGetConditionForVariation(123, 321, $stubbedCondition);

        $expectedData = [
            'id' => 123,
            'application' => 321
        ];

        // Expectations
        $this->mockService->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 123]);

        $this->assertEquals(123, $this->sut->save($data));
    }

    public function testSaveEditExisting()
    {
        $data = [
            'id' => 123,
            'application' => 321
        ];

        // AEU (Only actions we can edit)
        $stubbedCondition = [
            'id' => 123,
            'action' => 'E',
            'addedVia' => [
                'id' => 'case'
            ]
        ];

        $this->mockGetConditionForVariation(123, 321, $stubbedCondition);

        $expectedData = [
            'application' => 321,
            'addedVia' => 'case',
            'action' => 'U',
            'licConditionVariation' => 123
        ];

        // Expectations
        $this->mockService->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 789]);

        $this->assertEquals(789, $this->sut->save($data));
    }

    public function testDeleteWithoutAction()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'C'
            ],
            [
                'id' => 456,
                'action' => 'D'
            ]
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->assertNull($this->sut->delete(123, $parentId));
        $this->assertNull($this->sut->delete(456, $parentId));
    }

    public function testDeleteWithDelete()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'A'
            ],
            [
                'id' => 456,
                'action' => 'U'
            ]
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->mockService->shouldReceive('delete')
            ->with(123)
            ->shouldReceive('delete')
            ->with(456);

        $this->assertNull($this->sut->delete(123, $parentId));
        $this->assertNull($this->sut->delete(456, $parentId));
    }

    public function testDeleteWithDelta()
    {
        $parentId = 321;

        $stubbedData = [
            [
                'id' => 123,
                'action' => 'E',
                'foo' => 'bar'
            ]
        ];
        $expectedData = [
            'foo' => 'bar',
            'application' => 321,
            'action' => 'D',
            'licConditionVariation' => 123
        ];

        $this->mockGetForVariation($parentId, $stubbedData);

        $this->mockService->shouldReceive('save')
            ->with($expectedData)
            ->andReturn(['id' => 789]);

        $this->assertNull($this->sut->delete(123, $parentId));
    }

    public function testProcessDataForSave()
    {
        $id = 123;
        $data = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
            ]
        ];
        $expected = [
            'fields' => [
                'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE,
                'operatingCentre' => null,
                'application' => 123,
                'isDraft' => 'Y'
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }

    /**
     * Need to test this to test getLicenceId
     */
    public function testAlterForm()
    {
        // Params
        $form = m::mock('\Zend\Form\Form');
        $id = 123;
        $licenceId = 321;
        $stubbedOcList = [
            'Results' => [
                [
                    'action' => 'A',
                    'operatingCentre' => [
                        'id' => 111,
                        'address' => [
                            'addressLine1' => '123 street',
                            'addressLine2' => 'foo bar town'
                        ]
                    ]
                ],
                [
                    'action' => 'D',
                    'operatingCentre' => [
                        'id' => 222,
                        'address' => [
                            'addressLine1' => '123 street',
                            'addressLine2' => 'foo bar town'
                        ]
                    ]
                ]
            ]
        ];
        $stubbedLocList = [
            'Results' => [
                [
                    'operatingCentre' => [
                        'id' => 222,
                        'address' => [
                            'addressLine1' => '123 street',
                            'addressLine2' => 'foo bar town'
                        ]
                    ]
                ],
                [
                    'operatingCentre' => [
                        'id' => 333,
                        'address' => [
                            'addressLine1' => '123 street',
                            'addressLine2' => 'foo bar town'
                        ]
                    ]
                ]
            ]
        ];
        $expectedOptions = [
            'Licence' => [
                'label' => 'Licence',
                'options' => [
                    ConditionUndertakingEntityService::ATTACHED_TO_LICENCE => 'Licence (654)'
                ]
            ],
            'OC' => [
                'label' => 'OC Address',
                'options' => [
                    333 => '123 street, foo bar town',
                    111 => '123 street, foo bar town'
                ]
            ]
        ];

        // Mocks
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockAoc = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAoc);
        $mockLoc = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLoc);

        // Expectations
        $mockApplicationEntity->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn(['licNo' => 654]);

        $mockAoc->shouldReceive('getOperatingCentreListForLva')
            ->with($id)
            ->andReturn($stubbedOcList);

        $mockLoc->shouldReceive('getOperatingCentreListForLva')
            ->with($licenceId)
            ->andReturn($stubbedLocList);

        $form->shouldReceive('get')
            ->with('fields')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('attachedTo')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setValueOptions')
                    ->with($expectedOptions)
                    ->getMock()
                )
                ->getMock()
            );

        $this->sut->alterForm($form, $id);
    }

    public function testAlterTable()
    {
        $table = m::mock('\Common\Service\Table\TableBuilder');

        $this->assertNull($this->sut->alterTable($table));
    }

    public function providerGetTableDataEmpty()
    {
        return [
            [
                [],
                []
            ],
            [
                [
                    [
                        'id' => 12,
                        'action' => 'A'
                    ],
                    [
                        'id' => 13,
                        'action' => null,
                        'variationRecords' => []
                    ],
                    [
                        'id' => 14,
                        'action' => null,
                        'variationRecords' => [
                            ['action' => 'D']
                        ]
                    ]
                ],
                [
                    [
                        'id' => 12,
                        'action' => 'A'
                    ],
                    [
                        'id' => 13,
                        'action' => 'E',
                        'variationRecords' => []
                    ]
                ],
            ]
        ];
    }

    public function providerDetermineAction()
    {
        return [
            'Added' => [
                ['action' => 'A'],
                'A'
            ],
            'Updated' => [
                ['action' => 'U'],
                'U'
            ],
            'Existing' => [
                ['action' => null, 'variationRecords' => []],
                'E'
            ],
            'Removed' => [
                ['variationRecords' => [['action' => 'D']]],
                'R'
            ],
            'Current' => [
                ['variationRecords' => [['action' => 'U']]],
                'C'
            ]
        ];
    }

    public function providerCanEditRecord()
    {
        return [
            'Added' => [
                ['action' => 'A'],
                true
            ],
            'Updated' => [
                ['action' => 'U'],
                true
            ],
            'Existing' => [
                ['action' => null, 'variationRecords' => []],
                true
            ],
            'Removed' => [
                ['variationRecords' => [['action' => 'D']]],
                false
            ],
            'Current' => [
                ['variationRecords' => [['action' => 'U']]],
                false
            ]
        ];
    }

    public function providerSaveEdit()
    {
        return [
            ['A'],
            ['U']
        ];
    }

    /**
     * Helper method to prevent duplicating mock expectations
     */
    protected function mockGetConditionForVariation($id, $parentId, $stubbedCondition)
    {
        $this->mockService->shouldReceive('getConditionForVariation')
            ->once()
            ->with($id, $parentId)
            ->andReturn($stubbedCondition);
    }

    /**
     * Helper method to prevent duplicating mock expectations
     */
    protected function mockGetForVariation($id, $stubbedData)
    {
        $this->mockService->shouldReceive('getForVariation')
            ->once()
            ->with($id)
            ->andReturn($stubbedData);
    }
}
