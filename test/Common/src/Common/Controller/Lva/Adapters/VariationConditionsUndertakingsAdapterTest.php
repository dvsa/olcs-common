<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter;
use Common\Service\Script\ScriptFactory;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class VariationConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $container;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->sut = new VariationConditionsUndertakingsAdapter($this->container);
    }

    public function testGetTableName()
    {
        $this->assertEquals('lva-variation-conditions-undertakings', $this->sut->getTableName());
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();
        $this->container->expects('get')->with(ScriptFactory::class)->andReturn($mockScript);

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
}
