<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtNoOfPermitsElement;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Filter\StringTrim;
use Zend\Validator\Digits;
use Zend\Validator\LessThan;
use Zend\Validator\StringLength;

/**
 * EcmtNoOfPermitsElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsElementTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $name = 'euro5Required';
        $max = 13;
        $maxExceededErrorMessage = 'There are only 13 permits available for the selected emissions standard';

        $options = [
            'max' => $max,
            'maxExceededErrorMessage' => $maxExceededErrorMessage
        ];

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
                [
                    'name' => LessThan::class,
                    'options' => [
                        'max' => $max,
                        'inclusive' => true,
                        'messages' => [
                            LessThan::NOT_LESS_INCLUSIVE => $maxExceededErrorMessage
                        ]
                    ]
                ]
            ]
        ];

        $ecmtNoOfPermitsElement = new EcmtNoOfPermitsElement($name);
        $ecmtNoOfPermitsElement->setOptions($options);

        $this->assertEquals(
            $expectedInputSpecification,
            $ecmtNoOfPermitsElement->getInputSpecification()
        );
    }
}
