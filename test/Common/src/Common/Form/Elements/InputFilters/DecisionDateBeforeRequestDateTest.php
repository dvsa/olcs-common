<?php

/**
 * Test DecisionDateBeforeRequestDate
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\DecisionDateBeforeRequestDate;
use Zend\Validator as ZendValidator;

/**
 * Test DecisionDateBeforeRequestDate Input Filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class DecisionDateBeforeRequestDateTest extends PHPUnit_Framework_TestCase
{
    /**+
     * The input filter
     */
    private $inputFilter;

    /**
     * Setup the input filter
     */
    public function setUp()
    {
        $this->inputFilter = new DecisionDateBeforeRequestDate();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->inputFilter->getInputSpecification();

        if (is_array($spec['validators']))
        {
            foreach ($spec['validators'] as $validator) {
                $validatorClasses[] = get_class($validator);
            }
        }

        $this->assertContains('Common\Form\Elements\Validators\DateNotInFuture', $validatorClasses);
        $this->assertContains('Common\Form\Elements\Validators\DateGreaterThanOrEqual', $validatorClasses);
        $this->assertContains('Zend\Validator\Date', $validatorClasses);
    }
}
