<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsCombinedTotalElement;
use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsCombinedTotalValidator;
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
                            Callback::INVALID_VALUE => 'qanda-ecmt-short-term.number-of-permits.error.total-max-exceeded'
                        ]
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [NoOfPermitsCombinedTotalValidator::class, 'validateMin'],
                        'messages' => [
                            Callback::INVALID_VALUE => 'qanda-ecmt-short-term.number-of-permits.error.total-min-exceeded'
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
