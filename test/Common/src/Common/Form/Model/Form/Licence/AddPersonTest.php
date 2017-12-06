<?php

namespace CommonTest\Form\Model\Form\Licence;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Element\Collection;

class AddPersonTest extends AbstractFormValidationTestCase
{
    /** @var string The class name of the form being tested */
    protected $formName = \Common\Form\Model\Form\Licence\AddPerson::class;


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

    public function testHasDataCollection()
    {
        $this->assertTrue($this->sut->has('data'));
        $this->assertInstanceOf(Collection::class, $this->sut->get("data"));
        $this->assertArraySubset(
            [
                'count' => '1',
                "hint" => "markup-add-another-director-hint",
                "hint-position" => "below",
                "should_create_template" => true,
            ],
            $this->sut->get('data')->getOptions()
        );
    }
}
