<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\NoOfPermitsCombinedTotalElement;
use Common\Form\Elements\Validators\NoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * NoOfPermitsCombinedTotalElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsCombinedTotalElementTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $name = 'euro5Required';
        $maxPermitted = 13;

        $options = [
            'maxPermitted' => $maxPermitted,
        ];

        $expectedInputSpecification = [
            'name' => $name,
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMax'],
                        'callbackOptions' => [$maxPermitted],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.max-exceeded'
                        ]
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMin'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.min-exceeded'
                        ]
                    ]
                ],
            ],
        ];

        $noOfPermitsCombinedTotalElement = new NoOfPermitsCombinedTotalElement($name);
        $noOfPermitsCombinedTotalElement->setOptions($options);

        $this->assertEquals(
            $expectedInputSpecification,
            $noOfPermitsCombinedTotalElement->getInputSpecification()
        );
    }
}
