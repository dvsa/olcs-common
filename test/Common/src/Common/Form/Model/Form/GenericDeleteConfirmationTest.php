<?php

namespace CommonTest\Form\Model\Form;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class GenericDeleteConfirmationTest
 *
 * @group FormTests
 */
class GenericDeleteConfirmationTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\GenericDeleteConfirmation::class;
}
