<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class LicenceHistoryTest
 *
 * @group FormTests
 */
class LicenceHistoryTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\LicenceHistory::class;

    public function testPrevHasLicenceTableTable()
    {
        $element = ['data', 'prevHasLicence-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevHasLicenceTableAction()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevHasLicence-table', 'action']
        );
    }

    public function testPrevHasLicenceTableRows()
    {
        $this->assertFormElementHidden(
            ['data', 'prevHasLicence-table', 'rows']
        );
    }

    public function testPrevHasLicenceTableId()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevHasLicence-table', 'id']
        );
    }

    public function testPrevHasLicence()
    {
        $this->assertFormElementRequired(
            ['data', 'prevHasLicence'],
            true
        );
    }

    public function testPrevHadLicence()
    {
        $this->assertFormElementRequired(
            ['data', 'prevHadLicence'],
            true
        );
    }

    public function testPrevHadLicenceTableTable()
    {
        $element = ['data', 'prevHadLicence-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevHadLicenceTableAction()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevHadLicence-table', 'action']
        );
    }

    public function testPrevHadLicenceTableRows()
    {
        $this->assertFormElementHidden(
            ['data', 'prevHadLicence-table', 'rows']
        );
    }

    public function testPrevHadLicenceTableId()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevHadLicence-table', 'id']
        );
    }

    public function testPrevBeenDisqualifiedTableTable()
    {
        $element = ['data', 'prevBeenDisqualifiedTc-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevBeenDisqualifiedTableAction()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevBeenDisqualifiedTc-table', 'action']
        );
    }

    public function testPrevBeenDisqualifiedTableRows()
    {
        $this->assertFormElementHidden(
            ['data', 'prevBeenDisqualifiedTc-table', 'rows']
        );
    }

    public function testPrevBeenDisqualifiedTableId()
    {
        $this->assertFormElementNoRender(
            ['data', 'prevBeenDisqualifiedTc-table', 'id']
        );
    }

    public function testPrevBeenDisqualifiedTc()
    {
        $this->assertFormElementRequired(
            ['data', 'prevBeenDisqualifiedTc'],
            true
        );
    }

    public function testQuestionsHint()
    {
        $this->assertFormElementHtml(['questionsHint', 'message']);
    }

    public function testPrevBeenRefusedTableTable()
    {
        $element = ['eu', 'prevBeenRefused-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevBeenRefusedTableAction()
    {
        $this->assertFormElementNoRender(
            ['eu', 'prevBeenRefused-table', 'action']
        );
    }

    public function testPrevBeenRefusedTableRows()
    {
        $this->assertFormElementHidden(['eu', 'prevBeenRefused-table', 'rows']);
    }

    public function testPrevBeenRefusedTableId()
    {
        $this->assertFormElementNoRender(['eu', 'prevBeenRefused-table', 'id']);
    }

    public function testPrevBeenRevokedTableTable()
    {
        $element = ['eu', 'prevBeenRevoked-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevBeenRevokedTableAction()
    {
        $this->assertFormElementNoRender(
            ['eu', 'prevBeenRevoked-table', 'action']
        );
    }

    public function testPrevBeenRevokedTableRows()
    {
        $this->assertFormElementHidden(['eu', 'prevBeenRevoked-table', 'rows']);
    }

    public function testPrevBeenRevokedTableId()
    {
        $this->assertFormElementNoRender(['eu', 'prevBeenRevoked-table', 'id']);
    }

    public function testEuPrevBeenRefused()
    {
        $this->assertFormElementRequired(['eu', 'prevBeenRefused'], true);
    }

    public function testEuPrevBeenRevoked()
    {
        $this->assertFormElementRequired(['eu', 'prevBeenRevoked'], true);
    }

    public function testPrevBeenAtPiTableTable()
    {
        $element = ['pi', 'prevBeenAtPi-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevBeenAtPiTableAction()
    {
        $this->assertFormElementNoRender(
            ['pi', 'prevBeenAtPi-table', 'action']
        );
    }

    public function testPrevBeenAtPiTableRows()
    {
        $this->assertFormElementHidden(['pi', 'prevBeenAtPi-table', 'rows']);
    }

    public function testPrevBeenAtPiTableId()
    {
        $this->assertFormElementNoRender(['pi', 'prevBeenAtPi-table', 'id']);
    }

    public function testPrevPurchasedAtPi()
    {
        $this->assertFormElementRequired(['pi', 'prevBeenAtPi'], true);
    }

    public function testPrevPurchasedAssetsTableTable()
    {
        $element = ['assets', 'prevPurchasedAssets-table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPrevPurchasedAssetsTableAction()
    {
        $this->assertFormElementNoRender(
            ['assets', 'prevPurchasedAssets-table', 'action']
        );
    }

    public function testPrevPurchasedAssetsTableRows()
    {
        $this->assertFormElementHidden(
            ['assets', 'prevPurchasedAssets-table', 'rows']
        );
    }

    public function testPrevPurchasedAssetsTableId()
    {
        $this->assertFormElementNoRender(
            ['assets', 'prevPurchasedAssets-table', 'id']
        );
    }

    public function testPrevPurchasedAssets()
    {
        $this->assertFormElementRequired(
            ['assets', 'prevPurchasedAssets'], true
        );
    }

    public function testSaveAndContinue()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'saveAndContinue']
        );
    }

    public function testSave()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'save']
        );
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'cancel']
        );
    }

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }
}
