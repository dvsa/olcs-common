<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class CommunityLicencesRestoreTest
 *
 * @group FormTests
 */
class CommunityLicencesRestoreTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\CommunityLicencesRestore::class;

    public function testConfirm()
    {
        $element = [ 'data', 'confirm' ];
        $this->assertFormElementHtml($element);
    }

    public function testActionButtons()
    {
        $element = [ 'form-actions', 'submit' ];
        $this->assertFormElementActionButton($element);

        $element = [ 'form-actions', 'cancel' ];
        $this->assertFormElementActionButton($element);
    }
}
