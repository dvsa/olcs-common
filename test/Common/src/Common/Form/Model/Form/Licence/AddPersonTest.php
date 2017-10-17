<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 17/10/2017
 * Time: 13:54
 */

namespace CommonTest\Form\Model\Form\Licence;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

class AddPersonTest extends AbstractFormValidationTestCase
{

    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Licence\AddPerson::class;



    public function testContinueToFinancialHistory()
    {
        $element = ['form-actions', 'continueToFinancialHistory'];
        $this->assertFormElementActionButton($element);
    }
}
