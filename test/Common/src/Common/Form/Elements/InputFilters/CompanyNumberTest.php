<?php

/**
 * Test CompanyNumber
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\CompanyNumber;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;

/**
 * Test CompanyNumber
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CompanyNumberTest extends PHPUnit_Framework_TestCase
{
    /**+
     * Holds the element
     */
    private $element;

    /**
     * Setup the element
     */
    public function setUp()
    {
        $this->element = new CompanyNumber();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();
        $expected = [
            'name' => null,
            'required' => true,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'Zend\Validator\StringLength',
                    'options'=> [
                        'min' => 1,
                        'max' => 8,
                        'messages' => [
                            StringLength::TOO_LONG => 'The company number cannot be more than 8 characters'
                        ]
                    ]
                ],
                [
                    'name' => 'Alnum',
                    'options' => [
                        'messages' => [
                            Alnum::NOT_ALNUM =>
                                'Must be 8 digits; alpha-numeric characters allowed; ' .
                                'no special characters; mandatory when displayed'
                        ],
                    ],
                ]
            ]
        ];
        $this->assertEquals($spec, $expected);
    }
}
