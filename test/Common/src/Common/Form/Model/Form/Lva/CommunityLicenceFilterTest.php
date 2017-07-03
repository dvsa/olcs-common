<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Validator\Identical;

/**
 * Class CommunityLicenceFilterTest
 *
 * @group FormTests
 */
class CommunityLicenceFilterTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\CommunityLicenceFilter::class;

    public function testStatus()
    {
        $element = [ 'status' ];
        $this->assertFormElementDynamicMultiCheckbox($element, true);
        $this->assertFormElementNotValid($element, 'X', [['notInArray' => 'The input was not found in the haystack']]);
    }

    public function testIsFiltered()
    {
        $element = [ 'isFiltered' ];
        $this->assertFormElementHidden($element);
    }

    public function testFilterButton()
    {
        $element = [ 'filter' ];
        $this->assertFormElementActionButton($element);
    }
}
