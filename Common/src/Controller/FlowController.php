<?php

namespace Common\Controller;

use Common\Journey\Step;
use Common\Journey\Journey;
use Common\Journey\JourneyState;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class FlowController extends FormController
{
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $return = parent::dispatch($request, $response);

        if ($return instanceof Step) {
            $return = $this->redirectToStep($return);
        }

        return $return;
    }

    public function redirectToStep(Step $step)
    {
        return $this->redirect()
                    ->toRoute($step->getRoute()->getMatchedRouteName(), 
                              $step->getRoute()->getParams());
    }

    public function isEnhanced()
    {
        return $this->params()->fromRoute('state') == JourneyState::ALL;
    }

    public function getNextStep($journey, $model)
    {
        if ($this->isEnhanced() || !$this->getRequest()->isPost() || !$journey->isValid()) {
            return false;
        }

        return $this->getActiveStep($journey)->getNextStep($model);
    }

    /**
     * @param Journey $journey
     * @return type
     */
    public function getActiveStep($journey)
    {
        return $journey->getActiveStep($this->getEvent()->getRouteMatch());
    }

    public function getActiveSteps($journey)
    {
        if ($this->isEnhanced()) {
            return $journey->getSteps();
        } else {
            return [$this->getActiveStep($journey)];
        }
    }

    public function getActiveForms($journey)
    {
        return $journey->getActiveForms($this->getEvent()->getRouteMatch());
    }

    /**
     * Just a wrapper around a service locator call 
     * It keeps the journey-fetching implementation local to the FlowController
     * so children do no have to manage this themselves. 
     * 
     * @param type $definition
     * @return \Olcs\Common\Journey\FormJourney
     */
    public function getJourney($definition)
    {
        return $this->getServiceLocator()->get('Journey')->from($definition, $this->getServiceLocator()->get('OlcsForm'));
    }

    /**
     * Provides a standard ViewModel for flows,
     * defining a $model, $step, and $incomplete
     * 
     * Journies define their own completeness and active models
     * So this default model is a basic forwarding of 
     * journey state to the view
     * 
     * @param Journey $journey
     * @param \Olcs\Common\Model\FlowModelAbstract $model
     * @param array $forms
     * @return \Zend\View\Model\ViewModel
     */
    public function getFlowViewModel($journey, $model, $forms)
    {
        $this->layout()->setVariable('route', $this->getEvent()->getRouteMatch());
        
        return $this->getServiceLocator()
                    ->get('ViewModel')
                    ->setVariable('model', $model)
                    ->setVariable('steps', $this->getActiveSteps($journey))
                    ->setVariable('incomplete', $this->getFieldNames($this->getIncompletes($journey, $model)))
                    ->setVariables($this->getViewForms($forms));
    }

    public function getViewForms($forms) {
        $viewVars = [];
        
        foreach($forms as $name => $form) {
            $viewName = lcfirst(implode(array_map('ucfirst', explode('-', $name))));
            $viewVars[$viewName] = $form;
        }
        
        return $viewVars;
    }
    
    public function getIncompletes($journey, $model)
    {
        $extractedModel = $this->getModelExtract($model);
        if (!$journey->isComplete($extractedModel)) {
            return $journey->getIncomplete($extractedModel);
        } else {
            return [];
        }
    }

}
