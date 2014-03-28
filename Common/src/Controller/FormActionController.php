<?php
/**
 * An abstract controller that all ordinary OLCS controllers inherit from.
 * Provides a user journey form flow for generating pages.
 *
 * @package     olcscommon
 * @subpackage  controller
 * @author      Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Controller;
use Common\Controller\AbstractActionController;

use Zend\Session\Container;

abstract class FormActionController extends AbstractActionController
{
    /**
     * Method that is called at the end of a journey.
     */
    abstract function completeAction(); // must return \Zend\ViewModel

    /**
     * Persists the form data for a section.
     * 
     * @param type $section
     * @param type $form
     */
    public function persistFormData($form)
    {
       $step = $this->getCurrentStep();
       
       $formName = $form->getName();

       $session = new Container($formName);

       $data = $form->getData();
       $session->$step = $data;
    }
    
    /**
     * Gets the current persisted form data for the form within the passed 
     * section.
     * 
     * @param type $section
     * @param type $form
     * @return array
     */
    public function getPersistedFormData($form)
    {
       $step = $this->getCurrentStep();        
       $formName = $form->getName();

       $session = new Container($formName);

       return $session->$step;
    }
    
    /**
     * Adds/Removes the relevent fieldset/elements to the form. Determined by $step
     * Returns the form with configured with the correct fields for the step.
     * 
     * @param string $section
     * @param \Zend\Form $form
     * @return \Zend\Form
     */
    public function configureForm($formGenerator, $formConfig)
    {
        $step = $this->getCurrentStep();
        $section = $this->getCurrentSection();
        $formConfig[$section] = $formGenerator->addFieldset($formConfig[$section],$step);

        return $formConfig;
    }
    
    /**
     * Loads the form config file NOT USED
     * @param string $section
     * @return array
     *
    private function loadFormConfig($section)
    {
        if (!file_exists(__DIR__.'/../Form/Forms/'.$section.'.form.php')) {
            throw new \Exception("Form $section has no specification config!");
        }
        $formConfig = include __DIR__.'/../Form/Forms/'.$section.'.form.php';
        return $formConfig;
    }*/
    
    /**
     * Method to return the current step
     * 
     * @return string
     */
    protected function getCurrentStep()
    {
        return $this->getEvent()->getRouteMatch()->getParam('step');
    }
   
    /**
     * Returns the section of the application where this form resides.
     * Set in the controller that processes the form
     * E.g. section = licence_type if the journey relate to the licence type
     * @return string
     */
    protected function getCurrentSection()
    {
        return $this->section;
    }
    
    /**
     * Determines the next step. The next step is used to redirect to a url
     * This needs to work from the config file for the form and look at 
     * What data is required against what we have persisted.
     * 
     * @param \Zend\Form $form
     * @throws \RuntimeException
     * @return string Next step
     */
    protected function evaluateNextStep($form)
    {
        $formData = $form->getData($this->getCurrentStep());   
        foreach($form->getFieldsets() as $fieldset)
        {
            $next_step_options = $fieldset->getOption('next_step');
            foreach($fieldset->getElements() as $element)
            {
                if (isset($next_step_options))
                {
                    return $next_step_options[$element->getValue()];
                }
                
            }
        }   
        throw new \RuntimeException('Next step not defined');        
    }
    
    /**
     * Wrapper function to generate a form based on the current section. It 
     * also adds the neccessary fieldsets required for the step we are on.
     * 
     * @return \Zend\Form
     */
    public function generateSectionForm()
    {
        $formGenerator = $this->getFormGenerator();
        
        $section = $this->getCurrentSection();
        
        // get initial form
        $stepFormConfig = $formGenerator->getFormConfig($section);

        // manipulate it
        $stepFormConfig = $this->configureForm($formGenerator, $stepFormConfig);
        
        // set form config on formGenerator
        $formGenerator->setFormConfig($stepFormConfig);
        
        // create form
        $stepForm = $formGenerator->createForm($section);
                
        return $stepForm;
    }
    
    /**
     * Default action to process a form. Can be overridden for more complex
     * form scenarios. By default it routes a user through a series of 
     * pre-configured forms and redirects to a next_step until the journey is 
     * 'complete' at which point it calls the complete action.
     * 
     * @param array $valid_data
     * @param \Zend\Form $form
     * @param array $journeyData
     * @param array $params
     * @return void
     */
    public function processForm($valid_data, $form, $journeyData, $params)
    {
        $this->persistFormData($form);

        $next_step = $this->evaluateNextStep($form);
        if ($next_step == 'complete')
        {
            return $this->forward()->dispatch('SelfServe\LicenceType\Index', array('action' => 'complete'));
        } else {
            $this->redirect()->toUrl($next_step);
        }
            
    }
}
