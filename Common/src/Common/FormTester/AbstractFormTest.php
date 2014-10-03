<?php

namespace Common\FormTester;

use Common\Form\Element\DynamicSelect;
use Mockery as m;

/**
 * Class AbstractFormTest
 * @package Common\FormTester
 */
abstract class AbstractFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $formName;

    /**
     * @var \Zend\Form\Form
     */
    protected $form;

    /**
     * This test is used to test each form field with each of the values provided for it
     *
     * @dataProvider provideTestFormFields
     *
     * @param $validationGroup
     * @param $data
     * @param $valid
     */
    public function testFormFields($validationGroup, $data, $valid)
    {
        $form = $this->getForm();
        $form->setValidationGroup($validationGroup);
        $form->setData($data);

        $this->assertEquals($valid, $form->isValid());
    }

    /**
     * @return array
     */
    public function provideTestFormFields()
    {
        $formData = $this->getFormData();

        $returnData = [];

        foreach ($formData as $field) {
            /** @var Data\Object\Test $field */
            $validationGroup = $field->getStack()->getTransposed();
            foreach ($field->getValues() as $value) {
                /** @var Data\Object\Value $value */
                $returnData[] = [$validationGroup, $value->getData($field->getStack()), $value->isValid()];
            }
        }

        return $returnData;
    }

    /**
     * This test ensures that every field in the form has at least one valid and one invalid test. Any field that is
     * missing one or both will generate an Incomplete test notification.
     *
     * @dataProvider provideTestCompleteness
     *
     * @param $field
     * @param $noTests
     */
    public function testCompleteness($field, $noTests)
    {
        if ($field) {
            if ($noTests) {
                $this->markTestIncomplete($this->formName . '::' . $field . ' has no tests');
            } else {
                $this->markTestIncomplete(
                    $this->formName . '::' . $field . ' is missing either a valid or invalid test'
                );
            }
        }
        $this->assertTrue(true); //form is completely tested
    }

    /**
     * @return array
     */
    public function provideTestCompleteness()
    {
        $formFields = Utils::extractFields($this->getForm());

        $data = [];

        foreach ($this->getFormData() as $field) {
            /** @var Data\Object\Test $field */
            $data = array_merge_recursive($data, $field->getStack()->getTransposed($field->isComplete()));
        }

        $diff = Utils::fullArrayDiffRecursive($formFields, $data);

        $provided = [];
        foreach (Utils::flatten($diff) as $key => $value) {
            $provided[] = [$key, $value];
        }

        if (empty($provided)) {
            $provided[] = [false, false];
        }
        return $provided;
    }

    /**
     * @return \Zend\Form\Form
     */
    protected function getForm()
    {
        if (is_null($this->form)) {
            $serviceManager = $this->getServiceManager();
            $serviceManager->setAllowOverride(true);

            $serviceManager->get('FormElementManager')->setFactory(
                'DynamicSelect',
                function ($serviceLocator, $name, $requestedName) {
                    $element = new DynamicSelect();
                    $element->setValueOptions(['key'=>'value']);
                    return $element;
                }
            );

            $this->form = $serviceManager->get('FormAnnotationBuilder')->createForm($this->formName);

            foreach ($this->getDynamicSelectData() as $dyanamicData) {
                list($stack, $data) = $dyanamicData;

                $element = $this->form;

                foreach ($stack as $name) {
                    $element = $element->get($name);
                }

                $element->setValueOptions($data);
            }
        }

        return $this->form;
    }


    /**
     * @return array
     */
    protected function getDynamicSelectData()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getFormData()
    {
        return [];
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    abstract protected function getServiceManager();
}
