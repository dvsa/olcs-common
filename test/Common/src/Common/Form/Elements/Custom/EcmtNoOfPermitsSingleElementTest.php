<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtNoOfPermitsElement;
use Common\Form\Elements\Custom\EcmtNoOfPermitsSingleElement;
use Common\Service\Qa\Custom\Ecmt\NoOfPermitsSingleValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Validator\GreaterThan;

/**
 * EcmtNoOfPermitsSingleElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsSingleElementTest extends MockeryTestCase
{
    /**
     * @dataProvider dpGetInputSpecification
     */
    public function testGetInputSpecification($emissionsCategory)
    {
        $maxPermitted = 77;
        $permitsRemaining = 22;

        $ecmtNoOfPermitsSingleElement = m::mock(EcmtNoOfPermitsSingleElement::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $parentInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
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

        $ecmtNoOfPermitsSingleElement->shouldReceive('callParentGetInputSpecification')
            ->withNoArgs()
            ->andReturn($parentInputSpecification);

        $expectedInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
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
                    'name' => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                        'messages' => [
                            GreaterThan::NOT_GREATER => EcmtNoOfPermitsSingleElement::GENERIC_ERROR_KEY
                        ]
                    ]
                ],
                [
                    'name' => NoOfPermitsSingleValidator::class,
                    'options' => [
                        'maxPermitted' => $maxPermitted,
                        'permitsRemaining' => $permitsRemaining,
                        'emissionsCategory' => $emissionsCategory,
                    ]
                ]
            ],
            'key3' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        ];

        $ecmtNoOfPermitsSingleElement->setOption('maxPermitted', $maxPermitted);
        $ecmtNoOfPermitsSingleElement->setOption('permitsRemaining', $permitsRemaining);
        $ecmtNoOfPermitsSingleElement->setOption('emissionsCategory', $emissionsCategory);

        $this->assertInstanceOf(EcmtNoOfPermitsElement::class, $ecmtNoOfPermitsSingleElement);

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtNoOfPermitsSingleElement->getInputSpecification()
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
