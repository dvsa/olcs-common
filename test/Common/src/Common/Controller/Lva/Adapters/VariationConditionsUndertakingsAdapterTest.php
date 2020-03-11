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
