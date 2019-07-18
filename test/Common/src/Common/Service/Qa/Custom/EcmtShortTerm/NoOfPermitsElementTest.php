<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\NoOfPermitsElement;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Filter\StringTrim;
use Zend\Validator\Digits;
use Zend\Validator\LessThan;
use Zend\Validator\StringLength;

/**
 * NoOfPermitsElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsElementTest extends MockeryTestCase
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
                            Digits::NOT_DIGITS => 'qanda-ecmt-short-term.number-of-permits.error.category-not-whole-number'
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

        $noOfPermitsElement = new NoOfPermitsElement($name);
        $noOfPermitsElement->setOptions($options);

        $this->assertEquals(
            $expectedInputSpecification,
            $noOfPermitsElement->getInputSpecification()
        );
    }
}
