<?php

/**
 * Test PreviousHistoryPenaltiesConvictionsPrevConvictionValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\PreviousHistoryPenaltiesConvictionsPrevConvictionValidator;

/**
 * Test PreviousHistoryPenaltiesConvictionsPrevConvictionValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PreviousHistoryPenaltiesConvictionsPrevConvictionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new PreviousHistoryPenaltiesConvictionsPrevConvictionValidator();
    }

    /**
     * Test isValid
     */
    public function testIsValid()
    {
        $this->validator->setRows(1);
        $this->assertEquals(true, $this->validator->isValid('Y', array()));

        $this->validator->setRows(0);
        $this->assertEquals(false, $this->validator->isValid('Y', array()));

        $this->validator->setRows(1);
        $this->assertEquals(true, $this->validator->isValid('N', array()));

        $this->validator->setRows(0);
        $this->assertEquals(true, $this->validator->isValid('N', array()));
    }
}
