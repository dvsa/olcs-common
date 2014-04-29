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

use Common\Controller\FormActionController;
use Zend\Session\Container;

abstract class FormJourneyActionController extends FormActionController
{

    protected $currentStep;
    protected $currentSection;
    
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
        /*$step = $this->getCurrentStep();

        $formName = $form->getName();

        $session = new Container($formName);

        $data = $form->getData();
        $session->$step = $data;
         */
         
    }

    /**
     * Gets the persisted form data for current step
  
     * @throws \Common\Exception\Exception
     * @return array
     */
    public function getPersistedFormData()
    { 
        $stepCamelCase = str_replace('-', ' ', $this->getCurrentStep());
        $stepCamelCase = ucwords($stepCamelCase);
        $stepCamelCase = str_replace(' ', '', $stepCamelCase);
        
        $methodName = sprintf("get%sFormData", $stepCamelCase);
        $callback = array($this, $methodName);
        
        if (is_callable($callback) && method_exists($callback[0], $callback[1])){
            $persistedData = call_user_func($callback);
            if (!is_array($persistedData)){
                throw new \Common\Exception\Exception('Invalid data returned from method: ' . $methodName);
            }
            return $persistedData;
        }
        
        return array();
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
        if (isset($formConfig[$section]['fieldsets'])) 
        {
            $formConfig[$section] = $formGenerator->addFieldset($formConfig[$section], $step);
        }
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
      } */

    /**
     * Method to return the current step
     *
     * @return string
     */
    protected function getCurrentStep()
    {
        return $this->currentStep;        
   }

    /**
     * Method to set the current step
     *
     * @return object
     */
    protected function setCurrentStep($step)
    {
        $this->currentStep = $step;
        return $this;
    }
    
    /**
     * Returns the section of the application where this form resides.
     * Set in the controller that processes the form
     * E.g. section = licence_type if the journey relate to the licence type
     * @return string
     */
    protected function getCurrentSection()
    {
        return $this->currentSection;
    }

    /**
     * Method to set the current section
     *
     * @return object
     */
    protected function setCurrentSection($section)
    {
        $this->currentSection = $section;
        return $this;
    }
    
    /**
     * Determines the next step. The next step is used to redirect to a url
     * This needs to work from the config file for the form and look at
     * What data is required against what we have persisted. If not found, default step is returned
     *
     * @param \Zend\Form $form
     * @throws \RuntimeException
     * @return string Next step
     */
    protected function evaluateNextStep($form)
    {
        $formData = $form->getData($this->getCurrentStep());
        foreach ($form->getFieldsets() as $fieldset) {
            $next_step_options = $fieldset->getOption('next_step')['values'];
            
            foreach ($fieldset->getElements() as $element) {
                $element_value = $element->getValue();
                if (isset($next_step_options[$element_value]) && !empty($next_step_options[$element_value])) {
                    return $next_step_options[$element->getValue()];
                }
            }
            if (isset($fieldset->getOption('next_step')['default']))
                return $fieldset->getOption('next_step')['default'];
        }
        throw new \RuntimeException('Next step not defined, for any elements');
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

        if ($next_step == 'complete') {
            return $this->forward()->dispatch('SelfServe\LicenceType\Index', array('action' => 'complete'));
        } else {
            $this->redirect()->toUrl($next_step);
        }
    }

    /**
     * Adds data to the array passed to the formPost callback
     *
     * @return array
     */
    protected function getCallbackData()
    {
        return array('journeyData' => $this->getJourneyData());
    }

    /**
     * Method to gather any info relevent to the journey. This is passed
     * to the processForm method and any call back used.
     *
     * @return array
     */
    private function getJourneyData()
    {
        return [
            'section' => $this->getCurrentSection(),
            'step' => $this->getCurrentStep()
        ];
    }

    /**
     * Method to determine the form that was posted. Searches all posted items
     * and if any start with 'submit_' then the remaining string is returned
     * to signify the submitted button pressed.
     * 
     * @param \Zend\Http\Request $request
     * @return string
     */
    protected function determineSubmitButtonPressed(\Zend\Http\Request $request)
    {
        $form_posted = '';
        if ($request->isPost()) 
        {
            $posted_data = $request->getPost($this->getCurrentStep());
            if (is_array($posted_data))
            {
                foreach($posted_data as $key => $value)
                {
                    if (substr($key, 0, 7) == 'submit_')
                    {
                        return substr($key, 7);
                    }
                }
            }
        }            
        return $form_posted;
    }
    
    protected function getStepProcessMethod($step)
    {
        // convert step to camelcase method
        $return = 'process';
        
        $step = str_replace('-', ' ', $step);
        $step = ucwords($step);
        $step = str_replace(' ', '', $step);
        return 'process'.$step;
    }
    
    /**
     * Get licence entity based on route id value
     *
     * @return array|object
     */
    protected function _getLicenceEntity()
    {
        $applicationId = (int) $this->params()->fromRoute('applicationId');

        $bundle = array(
            'children' => array(
                'licence',
            ),
        );

        $application = $this->makeRestCall('Application', 'GET', array('id' => $applicationId), $bundle);
        return $application['licence'];
     }
    
}
