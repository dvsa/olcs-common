<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Laminas\Form\Element\Select;

/**
 * Class VehiclesTransferTest
 *
 * @group FormTests
 */
class VehiclesTransferTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\VehiclesTransfer::class;

    public function testLicence()
    {
        $element = ['data', 'licence'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testTransferActionButton()
    {
        $element = ['form-actions', 'transfer'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancelButton()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
