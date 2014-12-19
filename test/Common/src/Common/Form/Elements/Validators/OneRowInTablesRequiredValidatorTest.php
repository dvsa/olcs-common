<?php

/**
 * Test OneRowInTablesRequiredValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\Lva\OneRowInTablesRequiredValidator;

/**
 * Test OneRowInTablesRequiredValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OneRowInTablesRequiredValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OneRowInTablesRequiredValidator();
    }

    /**
     * Test isValid
     *
     * @group OneRowInTablesRequiredValidator
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $rows, $crud, $expected)
    {
        $this->validator->setRows($rows);
        $this->validator->setCrud($crud);
        $this->assertEquals($expected, $this->validator->isValid($value));
    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return [
            ['Y', [0], false, false],
            ['Y', [0], true, true],
            ['Y', [1], true, true],
            ['Y', [1], false, true],

            ['N', [0], false, true],
            ['N', [0], true, true],
            ['N', [1], true, true],
            ['N', [1], false, true]
        ];
    }

    /**
     * Test set label
     *
     * @group OneRowInTablesRequiredValidator
     */
    public function testSetLabel()
    {
        $label = 'label';
        $this->validator->setLabel($label);
        $this->assertEquals($label, $this->validator->getLabel());
    }
}
