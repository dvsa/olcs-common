<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtNoOfPermitsCombinedTotalElement;
use Common\Form\Elements\Validators\EcmtNoOfPermitsCombinedTotalValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Callback;

/**
 * EcmtNoOfPermitsCombinedTotalElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsCombinedTotalElementTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $name = 'euro5Required';

        $expectedInputSpecification = [
            'name' => $name,
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateNonZeroValuePresent'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.no-fields-populated'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtNoOfPermitsCombinedTotalValidator::class,
                            'validateMultipleNonZeroValuesNotPresent'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.no-of-permits.error.two-or-more-fields-populated'
                        ]
                    ]
                ],
            ],
        ];

        $ecmtNoOfPermitsCombinedTotalElement = new EcmtNoOfPermitsCombinedTotalElement($name);

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtNoOfPermitsCombinedTotalElement->getInputSpecification()
        );
    }
}
