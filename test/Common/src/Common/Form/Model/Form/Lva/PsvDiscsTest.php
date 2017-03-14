<?php

namespace CommonTest\Form\Model\Form\Lva;

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
        $element = [ 'table', 'table' ];
        $this->assertFormElementTable($element);
        $this->assertFormElementNotValid($element, null, [ 'required' ]);

        $element = [ 'table', 'action' ];
        $this->assertFormElementHidden($element);

        $element = [ 'table', 'rows' ];
        $this->assertFormElementHidden($element);

        $element = [ 'table', 'id' ];
        $this->assertFormElementHidden($element);
    }
}
