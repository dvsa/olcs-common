<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class PsvDiscsTest
 *
 * @group FormTests
 */
class PsvDiscsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvDiscs::class;

    public function testTable()
    {
        $this->assertFormElementTable(['table', 'table']);
        $this->assertFormElementHidden(['table', 'action']);
        $this->assertFormElementHidden(['table', 'id']);

        $element = ['table', 'rows'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element);
    }
}
