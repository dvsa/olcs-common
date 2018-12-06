<?php

namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters\CompanyNumber;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;

/**
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @covers Common\Form\Elements\InputFilters\CompanyNumber
 */
class CompanyNumberTest extends \PHPUnit\Framework\TestCase
{
    /** @var  CompanyNumber */
    private $element;

    public function setUp()
    {
        $this->element = new CompanyNumber();
    }

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
                            StringLength::TOO_LONG => 'common.form.validation.company_number.too_long',
                        ],
                    ],
                ],
                [
                    'name' => 'Alnum',
                    'options' => [
                        'messages' => [
                            Alnum::NOT_ALNUM => 'common.form.validation.company_number.not_alnum',
                        ],
                    ],
                ]
            ]
        ];

        $this->assertEquals($spec, $expected);
    }
}
