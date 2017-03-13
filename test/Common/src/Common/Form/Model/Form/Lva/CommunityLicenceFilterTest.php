<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

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
        $this->assertFormElementDynamicMultiCheckbox($element, false);
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
