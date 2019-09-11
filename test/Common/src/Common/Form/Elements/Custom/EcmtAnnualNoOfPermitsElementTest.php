<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Custom\EcmtAnnualNoOfPermitsElement;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Filter\StringTrim;
use Zend\Validator\Digits;
use Zend\Validator\StringLength;

/**
 * EcmtAnnualNoOfPermitsElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtAnnualNoOfPermitsElementTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $name = 'euro5Required';

        $expectedInputSpecification = [
            'name' => $name,
            'required' => false,
            'filters' => [
                [
                    'name' => StringTrim::class
                ]
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 4,
                        'break_chain_on_failure' => true,
                    ]
                ],
                [
                    'name' => Digits::class,
                    'options' => [
                        'break_chain_on_failure' => true,
                        'messages' => [
                            Digits::NOT_DIGITS => 'permits.page.no-of-permits.error.not-whole-number'
                        ]
                    ]
                ],
            ]
        ];

        $ecmtAnnualNoOfPermitsElement = new EcmtAnnualNoOfPermitsElement($name);

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtAnnualNoOfPermitsElement->getInputSpecification()
        );
    }
}
