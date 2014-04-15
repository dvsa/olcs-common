<?php
/**
 * Test custom form generator
 * Author: Michael Cooper
 * 
 */

namespace CommonTest\Controller;
/**
 * @group form
 */
class OlcsCustomFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        $this->serviceManager = \OlcsTest\Bootstrap::getServiceManager();
        $this->customFormGenerator = $this->serviceManager->get('OlcsCustomForm');
        
        $this->formConfig = [
            'testConfig' => [
                'name' => 'testForm',
                'attributes' => [
                    'method' => 'post',
                ],
                'fieldsets' => [
                    [
                    'name' => 'search',
                    'options' => [
                        0
                    ],
                    'elements' => [
                            'compliance' => [
                            'type' => 'multicheckbox',
                            'label' => 'Compliance',
                            'value_options' => 'case_categories_compliance',
                            'required' => false,
                            'class' => 'blah',
                            'placeholder' => 'test text',
                            'attributes' => [
                                'value' => '10000'
                                ]
                            ]
                        ]
                    ],
                ],
                'elements' => [
                    'submit' => [
                        'type' => 'submit',
                        'label' => 'Next'
                    ]
                ]
            ]
        ];
        
        $this->blankFormConfig = [
            'testBlankForm' => [
                'name' => 'blankForm',
                'attributes' => [
                    'method' => 'post',
                ],
                'fieldsets' => [],

                'elements' => [
                    'submit' => [
                        'type' => 'submit',
                        'label' => 'Next'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate a valid form
     */
    public function testGetValidForm()
    {
        $this->customFormGenerator->setFormConfig($this->formConfig);
        $form = $this->customFormGenerator->createForm('testConfig');
        $this->assertTrue(get_class($form) === 'Zend\Form\Form');
        $this->assertTrue($form->getName() === 'testForm');
    }
    
    /**
     * Add a fieldset to a form config
     */
    public function testAddFieldsetForm()
    {
        
        $formConfig = $this->customFormGenerator->getFormConfig('licence_type');
        $formConfig = $this->customFormGenerator->addFieldset($this->blankFormConfig['testBlankForm'], 'operator_type');
        $this->customFormGenerator->setFormConfig(array('licence_type' => $formConfig));
        $form = $this->customFormGenerator->createForm('licence_type');
        $this->assertTrue(get_class($form) === 'Zend\Form\Form');
        $this->assertTrue($form->getName() === 'blankForm');
    }
    
    /**
     * Test for a form config that does not exist
     * @expectedException Exception
     */
    public function testGetFormException()
    {
        $form = $this->customFormGenerator->createForm('blahConfig');
    }
    
     /**
     * Add a fieldset to a form config
     ** @expectedException Exception
     */
    public function testAddFieldsetException()
    {
         $formConfig = $this->customFormGenerator->addFieldset($this->blankFormConfig['testBlankForm'], 'blahblah');
    }
}
