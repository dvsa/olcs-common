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

        $expectedInputSpecification = [
            'name' => $name,
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateNonZeroValuePresent'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda.ecmt-short-term.number-of-permits.error.no-fields-populated'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMultipleNonZeroValuesNotPresent'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda.ecmt-short-term.number-of-permits.error.two-or-more-fields-populated'
                        ]
                    ]
                ],
            ],
        ];

        $noOfPermitsCombinedTotalElement = new NoOfPermitsCombinedTotalElement($name);

        $this->assertEquals(
            $expectedInputSpecification,
            $noOfPermitsCombinedTotalElement->getInputSpecification()
        );
    }
}
