<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class LicenceHistoryLicenceTest
 *
 * @group FormTests
 */
class LicenceHistoryLicenceTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\LicenceHistoryLicence::class;

    public function testVersion()
    {
        $this->assertFormElementHidden(['data', 'version']);
    }

    public function testPreviousLicenceType()
    {
        $this->assertFormElementHidden(['data', 'previousLicenceType']);
    }

    public function testLicNo()
    {
        $this->assertFormElementRequired(['data', 'licNo'], true);
    }

    public function testHolderName()
    {
        $this->assertFormElementRequired(['data', 'holderName'], true);
    }

    public function testWillSurrender()
    {
        $this->assertFormElementRequired(['data', 'willSurrender'], true);
    }

    public function testDisqualificationDate()
    {
        $this->assertFormElementDate(['data', 'disqualificationDate']);
    }

    public function testDisqualificationLength()
    {
        $this->assertFormElementRequired(
            ['data', 'disqualificationLength'],
            false
        );
    }

    public function testPurchaseDate()
    {
        $this->assertFormElementDate(['data', 'purchaseDate']);
    }

    public function testAddAnother()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'addAnother']
        );
    }

    public function testSubmit()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'submit']
        );
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'cancel']
        );
    }
}
