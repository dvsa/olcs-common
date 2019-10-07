<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtAnnualNoOfPermitsCombinedTotalElement;
use Common\Form\Elements\Validators\EcmtAnnualNoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * EcmtAnnualNoOfPermitsCombinedTotalElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtAnnualNoOfPermitsCombinedTotalElementTest extends MockeryTestCase
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
                        'callback' => [EcmtAnnualNoOfPermitsCombinedTotalValidator::class, 'validateMax'],
                        'callbackOptions' => [$maxPermitted],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.max-exceeded'
                        ]
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [EcmtAnnualNoOfPermitsCombinedTotalValidator::class, 'validateMin'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.min-exceeded'
                        ]
                    ]
                ],
            ],
        ];

        $noOfPermitsCombinedTotalElement = new EcmtAnnualNoOfPermitsCombinedTotalElement($name);
        $noOfPermitsCombinedTotalElement->setOptions($options);

        $this->assertEquals(
            $expectedInputSpecification,
            $noOfPermitsCombinedTotalElement->getInputSpecification()
        );
    }
}
