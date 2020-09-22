<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Filter\NotPopulatedStringToZero;
use Common\Form\Elements\Custom\EcmtNoOfPermitsBothElement;
use Common\Form\Elements\Custom\EcmtNoOfPermitsElement;
use Common\Service\Qa\Custom\Ecmt\NoOfPermitsBothValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * EcmtNoOfPermitsBothElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsBothElementTest extends MockeryTestCase
{
    /**
     * @dataProvider dpGetInputSpecification
     */
    public function testGetInputSpecification($emissionsCategory)
    {
        $permitsRemaining = 55;

        $ecmtNoOfPermitsBothElement = m::mock(EcmtNoOfPermitsBothElement::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $parentInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ],
            'filters' => [
                [
                    'filter1key1' => 'filter1value1',
                    'filter1key2' => 'filter1value2',
                ],
            ],
            'validators' => [
                [
                    'validator1key1' => 'validator1value1',
                    'validator1key2' => 'validator1value2',
                ],
                [
                    'validator2key1' => 'validator2value1',
                    'validator2key2' => 'validator2value2',
                ]
            ],
            'key3' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        ];

        $ecmtNoOfPermitsBothElement->shouldReceive('callParentGetInputSpecification')
            ->withNoArgs()
            ->andReturn($parentInputSpecification);

        $expectedInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ],
            'filters' => [
                [
                    'filter1key1' => 'filter1value1',
                    'filter1key2' => 'filter1value2',
                ],
                [
                    'name' => NotPopulatedStringToZero::class
                ]
            ],
            'validators' => [
                [
                    'validator1key1' => 'validator1value1',
                    'validator1key2' => 'validator1value2',
                ],
                [
                    'validator2key1' => 'validator2value1',
                    'validator2key2' => 'validator2value2',
                ],
                [
                    'name' => NoOfPermitsBothValidator::class,
                    'options' => [
                        'permitsRemaining' => $permitsRemaining,
                        'emissionsCategory' => $emissionsCategory
                    ]
                ]
            ],
            'key3' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        ];

        $ecmtNoOfPermitsBothElement->setOption('permitsRemaining', $permitsRemaining);
        $ecmtNoOfPermitsBothElement->setOption('emissionsCategory', $emissionsCategory);

        $this->assertInstanceOf(EcmtNoOfPermitsElement::class, $ecmtNoOfPermitsBothElement);

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtNoOfPermitsBothElement->getInputSpecification()
        );
    }

    public function dpGetInputSpecification()
    {
        return [
            ['euro5'],
            ['euro6'],
        ];
    }
}
